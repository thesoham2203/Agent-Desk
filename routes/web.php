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

use App\Livewire\Requester\MyTicketsTable;
use App\Livewire\Requester\TicketCreateForm;
use App\Livewire\Requester\TicketDetail;

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
Route::middleware(['auth', 'role:requester,admin'])
    ->prefix('my')
    ->name('requester.')
    ->group(function (): void {
        Route::get('/tickets', TicketCreateForm::class)
            ->name('tickets.create');
        Route::get('/tickets/list', MyTicketsTable::class)
            ->name('tickets.index');
        Route::get('/tickets/{ticketId}', TicketDetail::class)
            ->name('tickets.show');
    });

// Auth routes provided by Breeze — do not remove
require __DIR__.'/auth.php';
