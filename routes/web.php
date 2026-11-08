<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// The arrow function keeps the root route as a short redirect to the named catalog route.
Route::get('/', fn () => redirect()->route('catalog'));

// Keep sign-in pages public while signed-in routes require authentication.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'form'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Student, librarian, and admin permissions are enforced at route-group boundaries.
Route::middleware('auth')->group(function () {
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
    // Laravel resolves {book} to a Book model from the controller's type-hinted argument.
    Route::get('/books/{book}', [CatalogController::class, 'show'])->name('books.show');
    Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])->middleware('role:student')->name('borrow');
    Route::get('/my-requests', [BorrowingController::class, 'mine'])->middleware('role:student')->name('mine');
    Route::patch('/my-requests/{borrowing}/return', [BorrowingController::class, 'requestReturn'])->middleware('role:student')->name('return.request');
    Route::post('/ai-recommend', [CatalogController::class, 'recommend'])->middleware('role:student')->name('ai');
    // Librarians manage inventory, categories, and borrowing decisions.
    Route::middleware('role:librarian')->prefix('librarian')->name('lib.')->group(function () {
        // Resource routing maps the standard book URLs to controller actions.
        Route::resource('books', BookController::class);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::get('/requests', [BorrowingController::class, 'index'])->name('requests');
        Route::patch('/requests/{borrowing}', [BorrowingController::class, 'decision'])->name('decision');
        Route::post('/requests/{borrowing}/remind', [BorrowingController::class, 'remind'])->name('remind');
        Route::patch('/requests/{borrowing}/confirm-return', [BorrowingController::class, 'confirmReturn'])->name('confirm-return');
    });
    // Admin-only pages expose system monitoring and audit history.
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
    });
});
