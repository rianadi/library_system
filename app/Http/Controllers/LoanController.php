<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Tampilkan daftar semua peminjaman (untuk admin).
     */
    public function index(Request $request)
    {
        $query = Loan::with(['user:id,name,email', 'book:id,title', 'approver:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%')
                         ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhereHas('book', function ($subQ) use ($search) {
                    $subQ->where('title', 'like', '%' . $search . '%');
                });
            });
        }

        $loans = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('loans.index', compact('loans'));
    }

    /**
     * Tampilkan pinjaman milik member yang sedang login.
     */
    public function myLoans()
    {
        $loans = Loan::select('id', 'user_id', 'book_id', 'loan_date', 'due_date', 'return_date', 'status', 'fine_amount', 'created_at')
            ->with('book:id,title,author')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('loans.my-loans', compact('loans'));
    }

    /**
     * Ajukan peminjaman buku.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);

        if (!$book->isAvailable()) {
            return back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }

        // Cegah peminjaman ganda
        $existingLoan = Loan::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingLoan) {
            return back()->with('error', 'Anda sudah meminjam atau mengajukan peminjaman buku ini.');
        }

        Loan::create([
            'user_id'   => auth()->id(),
            'book_id'   => $book->id,
            'loan_date' => now()->toDateString(),
            'due_date'  => now()->addDays(7)->toDateString(),
            'status'    => 'pending',
        ]);

        return back()->with('success', 'Permintaan peminjaman berhasil diajukan.');
    }

    /**
     * Setujui peminjaman oleh admin.
     */
    public function approve(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses.');
        }

        $book = $loan->book;
        if (!$book->isAvailable()) {
            return back()->with('error', 'Buku tidak tersedia.');
        }

        $book->decreaseAvailableCopies();

        $loan->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Peminjaman berhasil disetujui.');
    }

    /**
     * Tolak peminjaman oleh admin.
     */
    public function reject(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses.');
        }

        $loan->update([
            'status'      => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    /**
     * Proses pengembalian buku.
     */
    public function return(Loan $loan)
    {
        if (!in_array($loan->status, ['approved', 'overdue'])) {
            return back()->with('error', 'Peminjaman ini tidak dapat dikembalikan.');
        }

        $fine = $loan->calculateFine();

        $loan->update([
            'status'      => 'returned',
            'return_date' => now()->toDateString(),
            'fine_amount' => $fine,
        ]);

        $loan->book->increaseAvailableCopies();

        $message = 'Buku berhasil dikembalikan.';
        if ($fine > 0) {
            $message .= ' Denda: Rp ' . number_format($fine, 0, ',', '.');
        }

        return back()->with('success', $message);
    }

    /**
     * Perpanjang masa peminjaman.
     */
    public function extend(Loan $loan)
    {
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman aktif yang dapat diperpanjang.');
        }

        $loan->update([
            'due_date' => $loan->due_date->addDays(7),
        ]);

        return back()->with('success', 'Peminjaman berhasil diperpanjang 7 hari.');
    }
    public function printLoans(Request $request)
{
    $query = Loan::with(['user', 'book']);
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('search')) {
        $query->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%');
        })->orWhereHas('book', function ($q) use ($request) {
            $q->where('title', 'like', '%'.$request->search.'%');
        });
    }
    $loans = $query->latest()->get(); // tanpa pagination
    return view('loans.print', compact('loans'));
}
}