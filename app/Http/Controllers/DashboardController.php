<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::sum('available_copies'),
            'total_copies' => Book::sum('total_copies'),
            'category_count' => Category::count(),
            'total_members' => User::where('role', 'member')->count(),
            'active_loans' => Loan::whereIn('status', ['approved'])->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'overdue_loans' => Loan::where('status', 'approved')
                ->whereDate('due_date', '<', now()->toDateString())
                ->count(),
        ];

        $recentLoans = Loan::select('id', 'user_id', 'book_id', 'loan_date', 'due_date', 'status', 'created_at')
            ->with('user:id,name', 'book:id,title')
            ->latest()
            ->take(5)
            ->get();

        $popularBooks = Book::select('id', 'title', 'author')
            ->withCount('loans')
            ->orderByDesc('loans_count')
            ->take(5)
            ->get();

        $latestBooks = Book::select('id', 'title', 'author', 'available_copies', 'cover_image', 'created_at')
            ->latest()
            ->take(4)
            ->get();

        return view('dashboard', compact('stats', 'recentLoans', 'popularBooks', 'latestBooks'));
    }
}
