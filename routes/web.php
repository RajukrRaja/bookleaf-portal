<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;
Route::get('/', fn() => auth()->check() ? redirect(auth()->user()->role === 'admin' ? '/admin': '/author'): redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('register.store');
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:author'])->prefix('author')->name('author.')->group(function () {
    Route::get('/', [AuthorController::class, 'dashboard'])->name('dashboard');
    Route::get('/books', [AuthorController::class, 'books'])->name('books');
    Route::get('/tickets', [AuthorController::class, 'tickets'])->name('tickets');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply'])->name('tickets.reply');
    Route::get('/tickets/{ticket}/refresh', [TicketController::class, 'refresh'])->name('tickets.refresh');
});

Route::middleware([
    'auth',
    'role:admin'
])->prefix('admin/tickets')->name('admin.tickets.')->group(function () {
    Route::patch('/{ticket}', [TicketController::class, 'update'])->name('update');
    Route::post('/{ticket}/draft', [TicketController::class, 'regenerate'])->name('draft');
    Route::post('/{ticket}/send', [TicketController::class, 'sendDraft'])->name('send');
    Route::post('/{ticket}/note', [TicketController::class, 'note'])->name('note');
    Route::post('/{ticket}/assign', [TicketController::class, 'assign'])->name('assign');
});
