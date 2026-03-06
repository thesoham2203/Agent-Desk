<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: AuditLogViewer.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A read-only interface for reviewing the system's historical audit trail.
 *
 * WHY DOES IT EXIST?
 * To provide accountability and transparency by tracking all
 * sensitive actions performed by users in the system.
 *
 * HOW IT FITS IN THE APP:
 * Accessed via /admin/audit-log. Queries the audit_logs table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Pagination in Laravel allows large datasets to be displayed
 * in manageable chunks, improving performance and user experience.
 * ============================================================
 */
use App\Models\AuditLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class AuditLogViewer extends Component
{
    use WithPagination;

    /**
     * Search term for filtering the log action.
     */
    public string $search = '';

    /**
     * Dropdown filter for specific log actions.
     */
    public string $actionFilter = '';

    /**
     * Number of log entries to display per page.
     */
    public int $perPage = 20;

    /**
     * Validates that the user has viewing permissions upon initialization.
     */
    public function mount(): void
    {
        $this->authorize('view-audit-log');
    }

    /**
     * Computed property that returns a paginated list of audit logs.
     */
    #[Computed]
    public function logs(): LengthAwarePaginator
    {
        return AuditLog::query()
            ->with(['user', 'ticket'])
            ->when($this->search !== '', fn ($query) => $query->where('action', 'like', '%'.$this->search.'%'))
            ->when($this->actionFilter !== '', fn ($query) => $query->where('action', $this->actionFilter))
            ->latest()
            ->paginate($this->perPage);
    }

    /**
     * Resets pagination when the search term is updated.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Resets pagination when the action filter is updated.
     */
    public function updatedActionFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Renders the audit log view.
     */
    public function render(): View
    {
        return view('livewire.admin.audit-log-viewer', [
            'logList' => $this->logs,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Admin/AiRunsViewer.php]
 * WHY: Both files are read-only viewers for system activity traces.
 * ============================================================
 */
