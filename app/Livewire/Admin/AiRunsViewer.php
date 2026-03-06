<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: AiRunsViewer.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A read-only interface for monitoring AI agent executions.
 *
 * WHY DOES IT EXIST?
 * To allow administrators to debug AI performance and review
 * the history of automated triage and reply generation.
 *
 * HOW IT FITS IN THE APP:
 * Accessed via /admin/ai-runs. Queries the ai_runs table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * Eager loading with with() prevents the 'N+1 query problem' by
 * retrieving all related models in a single database query.
 * ============================================================
 */
use App\Enums\AiRunStatus;
use App\Enums\AiRunType;
use App\Models\AiRun;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class AiRunsViewer extends Component
{
    use WithPagination;

    /**
     * Filter by the execution status (queued, running, succeeded, failed).
     */
    public string $statusFilter = '';

    /**
     * Filter by the type of AI task (triage, reply_draft).
     */
    public string $typeFilter = '';

    /**
     * Number of run records to display per page.
     */
    public int $perPage = 20;

    /**
     * Authorizes the user before the component mounts.
     */
    public function mount(): void
    {
        $this->authorize('view-ai-runs');
    }

    /**
     * Retrieves a paginated list of AI runs based on active filters.
     */
    #[Computed]
    public function runs(): LengthAwarePaginator
    {
        return AiRun::query()
            ->with(['ticket', 'initiatedBy'])
            ->when($this->statusFilter !== '', function ($query): void {
                $status = AiRunStatus::tryFrom($this->statusFilter);
                if ($status) {
                    $query->where('status', $status);
                }
            })
            ->when($this->typeFilter !== '', function ($query): void {
                $type = AiRunType::tryFrom($this->typeFilter);
                if ($type) {
                    $query->where('run_type', $type);
                }
            })
            ->latest()
            ->paginate($this->perPage);
    }

    /**
     * Resets pagination when status filter changes.
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Resets pagination when type filter changes.
     */
    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Renders the AI runs history view.
     */
    public function render(): View
    {
        return view('livewire.admin.ai-runs-viewer', [
            'runList' => $this->runs,
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [routes/web.php]
 * WHY: After building the logic, we must wire these components to URLs.
 * ============================================================
 */
