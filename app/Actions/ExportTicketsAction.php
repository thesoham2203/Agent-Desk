<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ============================================================
 * FILE: ExportTicketsAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * An action that generates a CSV file containing all tickets in the system.
 *
 * WHY DOES IT EXIST?
 * To allow administrators to export data for external analysis or reporting.
 *
 * HOW IT FITS IN THE APP:
 * Called by a controller or Livewire component when an Admin clicks 'Export'.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * We use a StreamedResponse to handle large datasets without running out
 * of memory. This sends the file to the browser as it's being generated.
 * ============================================================
 */
final class ExportTicketsAction
{
    /**
     * Executes the CSV export.
     */
    public function execute(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tickets-export-'.now()->format('Y-m-d-His').'.csv"',
        ];

        return new StreamedResponse(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // 1. Add CSV Header
            fputcsv(
                $handle,
                [
                    'ID',
                    'Title',
                    'Requester',
                    'Assignee',
                    'Status',
                    'Priority',
                    'Category',
                    'Created At',
                ],
                escape: '\\'
            );

            // 2. Chunk through tickets to avoid memory issues
            Ticket::query()
                ->with(['requester', 'assignee', 'category'])
                ->chunk(200, function (Collection $tickets) use ($handle): void {
                    foreach ($tickets as $ticket) {
                        fputcsv(
                            $handle,
                            [
                                $ticket->id,
                                $ticket->title,
                                $ticket->requester->name,
                                $ticket->assignee->name ?? 'Unassigned',
                                $ticket->status->name,
                                $ticket->priority->name,
                                $ticket->category->name ?? 'N/A',
                                $ticket->created_at->toDateTimeString(),
                            ],
                            escape: '\\'
                        );
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }
}
