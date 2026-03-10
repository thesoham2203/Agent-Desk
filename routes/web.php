<?php

declare(strict_types=1);

/**
 * NOTE: The AI subsystem requires a queue worker to function.
 * Run in a separate terminal: php artisan queue:work --tries=3
 * Required .env: QUEUE_CONNECTION=database, GROQ_API_KEY=...
 */
use App\Http\Controllers\AttachmentController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * Attachment download route.
 * Protected by auth middleware only — the AttachmentController
 * itself calls Gate::authorize() for fine-grained policy check.
 * We don't use role middleware here because both requesters
 * AND agents need to download attachments.
 */
Route::middleware(['auth'])
    ->group(function (): void {
        Route::get(
            '/attachments/{attachment}/download',
            [AttachmentController::class, 'download']
        )->name('attachments.download');
    });

// Default pages provided by Breeze — do not remove
Route::view('/', 'welcome');

Route::get('/dashboard', function () {
    /** @var User $user */
    $user = auth()->user();

    return to_route($user->role->dashboardRoute());
})->middleware(['auth'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

use App\Livewire\Agent\TicketDetail as AgentTicketDetail;
use App\Livewire\Agent\TriageQueue;
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

/**
 * ============================================================
 * AGENT ROUTES
 * ============================================================
 * Middleware:
 * - auth: must be logged in
 * - role:agent,admin: only agents and admins
 *
 * Prefix: /agent
 * Name prefix: agent.
 * ============================================================
 */
Route::middleware(['auth', 'role:agent,admin'])
    ->prefix('agent')
    ->name('agent.')
    ->group(function (): void {
        // Triage queue: all unassigned New tickets
        Route::get('/queue', TriageQueue::class)
            ->name('queue');
        // Ticket detail: full management view for agents
        Route::get('/tickets/{ticket}', AgentTicketDetail::class)
            ->name('tickets.show');
    });

use App\Http\Controllers\Admin\ExportController;
use App\Livewire\Admin\AiRunsViewer;
use App\Livewire\Admin\AuditLogViewer;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\KbArticleManager;
use App\Livewire\Admin\MacroManager;
use App\Livewire\Admin\SlaConfigManager;

/**
 * ============================================================
 * ADMIN ROUTES
 * ============================================================
 * Middleware: auth + role:admin only
 * Prefix: /admin
 * Name prefix: admin.
 * ============================================================
 */
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/categories', CategoryManager::class)->name('categories');
        Route::get('/macros', MacroManager::class)->name('macros');
        Route::get('/sla', SlaConfigManager::class)->name('sla');
        Route::get('/kb-articles', KbArticleManager::class)->name('kb-articles');
        Route::get('/audit-log', AuditLogViewer::class)->name('audit-log');
        Route::get('/ai-runs', AiRunsViewer::class)->name('ai-runs');

        // CSV Export of all tickets
        Route::get('/export/tickets', [ExportController::class, 'tickets'])->name('export.tickets');
    });
// Auth routes provided by Breeze — do not remove
require __DIR__.'/auth.php';
