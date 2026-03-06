<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

/**
 * ============================================================
 * FILE: SlaConfigManager.php
 * LAYER: Livewire Component
 * ============================================================
 *
 * WHAT IS THIS?
 * A management component for the application's SLA (Service Level Agreement) targets.
 *
 * WHY DOES IT EXIST?
 * To ensure that business response and resolution targets are
 * configurable without code changes.
 *
 * HOW IT FITS IN THE APP:
 * Updates the single row in the sla_configs table.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * The mount() method in Livewire is executed when the component is
 * first initialized, making it ideal for loading existing database state.
 * ============================================================
 */
use App\Models\AuditLog;
use App\Models\SlaConfig;
use Illuminate\View\View;
use Livewire\Component;

final class SlaConfigManager extends Component
{
    /**
     * Target hours for the first response on a new ticket.
     */
    public int $firstResponseHours = 4;

    /**
     * Target hours for full resolution of a ticket.
     */
    public int $resolutionHours = 24;

    /**
     * Initializes the component state from the database.
     */
    public function mount(): void
    {
        $this->authorize('manage-sla-config');

        $config = SlaConfig::query()->first();

        if ($config instanceof SlaConfig) {
            $this->firstResponseHours = $config->first_response_hours;
            $this->resolutionHours = $config->resolution_hours;
        }
    }

    /**
     * Persists the updated SLA configuration and logs the change.
     */
    public function update(): void
    {
        $this->authorize('manage-sla-config');

        $this->validate([
            'firstResponseHours' => 'required|integer|min:1|max:168|lte:resolutionHours',
            'resolutionHours' => 'required|integer|min:1|max:720',
        ]);

        $config = SlaConfig::query()->first();

        if ($config instanceof SlaConfig) {
            $config->update([
                'first_response_hours' => $this->firstResponseHours,
                'resolution_hours' => $this->resolutionHours,
            ]);

            AuditLog::query()->create([
                'action' => 'sla_config.updated',
                'user_id' => auth()->id(),
                'new_values' => [
                    'first_response_hours' => $this->firstResponseHours,
                    'resolution_hours' => $this->resolutionHours,
                ],
            ]);

            session()->flash('success', 'SLA configuration updated successfully.');
        }
    }

    /**
     * Renders the settings view.
     */
    public function render(): View
    {
        return view('livewire.admin.sla-config-manager');
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Admin/KbArticleManager.php]
 * WHY: Continuing with administrative content management.
 * ============================================================
 */
