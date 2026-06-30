<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\BookExcelImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::select('id', 'book_code', 'title', 'author', 'isbn', 'year', 'category_id', 'total_copies', 'available_copies', 'cover_image')
            ->with('category:id,name', 'loans:id,user_id,book_id,status');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('isbn', 'like', '%' . $search . '%')
                  ->orWhere('book_code', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $books = $query->orderByDesc('created_at')->paginate(12)->withQueryString();
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Load only needed columns
        $book->load('category:id,name');
        $userLoan = auth()->check()
            ? $book->loans()
                ->select('id', 'user_id', 'book_id', 'due_date', 'status')
                ->where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->first()
            : null;

        return view('books.show', compact('book', 'userLoan'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books',
            'publisher' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'category_id' => 'nullable|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'location' => 'nullable|string|max:50',
        ]);

        $validated['available_copies'] = $validated['total_copies'];

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    public function import(Request $request, BookExcelImportService $importer)
    {
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'max:5120',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! $value instanceof \Illuminate\Http\UploadedFile || strtolower($value->getClientOriginalExtension()) !== 'xlsx') {
                        $fail('File harus berformat .xlsx.');
                    }
                },
            ],
        ]);

        try {
            $summary = $importer->import($request->file('excel_file')->getRealPath());
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Import gagal. Pastikan file Excel tidak rusak dan memakai format .xlsx.');
        }

        $message = "Import selesai: {$summary['imported']} buku berhasil ditambahkan.";

        if ($summary['skipped'] > 0 || $summary['failed'] > 0) {
            $message .= " {$summary['skipped']} baris dilewati, {$summary['failed']} baris gagal.";
        }

        return redirect()->route('books.index')
            ->with('success', $message)
            ->with('import_errors', $summary['errors']);
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'publisher' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'category_id' => 'nullable|exists:categories,id',
            'total_copies' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'location' => 'nullable|string|max:50',
        ]);

        $oldTotal = $book->total_copies;
        $newTotal = $validated['total_copies'];
        $oldAvailable = $book->available_copies;

        // Hitung selisih salinan
        $difference = $newTotal - $oldTotal;
        $newAvailable = $oldAvailable + $difference;

        // Pastikan available tidak minus dan tidak melebihi total baru
        $validated['available_copies'] = max(0, min($newAvailable, $newTotal));

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }

    public function printBarcode(Book $book)
    {
        $book->load('category:id,name');

        return view('books.barcode-print', compact('book'));
    }
    
    public function findByBarcode($code)
    {
        $book = Book::select(
            'id',
            'book_code',
            'title',
            'author',
            'isbn',
            'year',
            'category_id',
            'total_copies',
            'available_copies',
            'location'
        )
            ->with('category:id,name')
            ->where('book_code', $code)
            ->first();

        if (! $book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan.',
            ]);
        }

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'book_code' => $book->book_code,
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'year' => $book->year,
                'category' => $book->category?->name,
                'total_copies' => $book->total_copies,
                'available_copies' => $book->available_copies,
                'location' => $book->location,
            ],
        ]);
    }
}
