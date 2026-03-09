<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\ExportTicketsAction;
use App\Models\Ticket;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ============================================================
 * FILE: ExportController.php
 * LAYER: Controller
 * ============================================================
 *
 * WHAT IS THIS?
 * A standard controller that manages administrative data exports.
 *
 * WHY DOES IT EXIST?
 * To provide a secure endpoint for Admins only to trigger CSV exports.
 *
 * HOW IT FITS IN THE APP:
 * Protected by the 'auth' and 'role:admin' middleware in web.php.
 * ============================================================
 */
final class ExportController
{
    /**
     * Downloads the CSV of all tickets.
     */
    public function tickets(ExportTicketsAction $action): StreamedResponse
    {
        // One final manual check for admin privileges before exporting
        Gate::authorize('viewAny', Ticket::class);

        return $action->execute();
    }
}
