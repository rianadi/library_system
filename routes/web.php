<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Member Loan Routes
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('loans.my');

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // Books Management
        Route::get('/admin/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/admin/books', [BookController::class, 'store'])->name('books.store');
        Route::post('/admin/books/import', [BookController::class, 'import'])->name('books.import');
        Route::get('/admin/books/{book}/barcode', [BookController::class, 'printBarcode'])->name('books.barcode.print');
        Route::get('/admin/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/admin/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/admin/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
        Route::get('/admin/loans/print', [LoanController::class, 'printLoans'])->name('loans.print');

        // Categories Management
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Loans Management
        Route::get('/admin/loans', [LoanController::class, 'index'])->name('loans.index');
        Route::post('/admin/loans/offline', [LoanController::class, 'storeOffline'])->name('loans.offline.store');
        Route::post('/admin/loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/admin/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/admin/loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
        Route::post('/admin/loans/{loan}/extend', [LoanController::class, 'extend'])->name('loans.extend');

        Route::get('/books/barcode/{code}', [BookController::class, 'findByBarcode'])
            ->middleware('auth')
            ->name('books.barcode.find');

        // Users Management
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';
