<?php


use App\Http\Controllers\CheckinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSearchController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowController;


Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes by role
Route::middleware([RoleMiddleware::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'userList'])->name('userList');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/search', [UserController::class, 'search'])->name('search');

        Route::get('/print-preview', [UserController::class, 'printPreview'])->name('print.preview');
        Route::get('/print-pdf', [UserController::class, 'printPdf'])->name('print.pdf');

        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');

    });

    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [BookController::class, 'bookList'])->name('bookList');
        Route::get('/create', [BookController::class, 'create'])->name('create');
        Route::post('/store', [BookController::class, 'store'])->name('store');
        Route::get('/search', [BookController::class, 'search'])->name('search');

        // Show the edit form
        Route::get('/{book}/edit', [BookController::class, 'edit'])->name('edit');
        // Update the book stock
        Route::put('/{book}', [BookController::class, 'update'])->name('update');
        // Delete the book
        Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('borrow-records')->group(function () {
        Route::get('/', [BorrowController::class, 'index'])->name('borrow-records.index');
        Route::get('/create', [BorrowController::class, 'create'])->name('borrow-records.create');
        Route::post('/', [BorrowController::class, 'store'])->name('borrow-records.store');
        Route::put('/{id}/return', [BorrowController::class, 'returnBook'])->name('borrow-records.return');
        Route::put('/{id}/extend', [BorrowController::class, 'extendBook'])->name('borrow-records.extend');

    });



});


Route::middleware([RoleMiddleware::class . ':member'])->group(function () {
    Route::prefix('checkins')->name('checkins.')->group(function () {
        Route::get('/', [CheckinController::class, 'index'])->name('index');
        Route::post('/', [CheckinController::class, 'store'])->name('store');
        Route::post('/status', [CheckinController::class, 'checkStatus'])->name('checkStatus');
    });
});
