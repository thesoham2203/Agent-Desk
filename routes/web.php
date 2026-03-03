<?php

declare(strict_types=1);
use Illuminate\Support\Facades\Route;

// Default pages provided by Breeze — do not remove
Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

/**
 * ============================================================
 * REQUESTER ROUTES
 * ============================================================
 * Middleware:
 * - auth: user must be logged in
 * - role:requester,admin: only requesters and admins allowed
 *
 * Prefix 'my' → URLs like /my/tickets
 * Name prefix 'requester.' → route('requester.tickets.index')
 * ============================================================
 */
// TODO: Uncomment after running Day 4 Copilot prompt
// Route::middleware(['auth', 'role:requester,admin'])
//     ->prefix('my')
//     ->name('requester.')
//     ->group(function (): void {
//         Route::get('/tickets', MyTicketsTable::class)
//             ->name('tickets.index');
//         Route::get('/tickets/new', TicketCreateForm::class)
//             ->name('tickets.create');
//         Route::get('/tickets/{ticket}', TicketDetail::class)
//             ->name('tickets.show');
//     });

// Auth routes provided by Breeze — do not remove
require __DIR__.'/auth.php';
