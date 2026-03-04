<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AttachmentController.php
 * LAYER: Controller
 * ============================================================
 *
 * WHAT IS THIS?
 * Serves private attachment files to authorized users.
 *
 * WHY DOES IT EXIST?
 * To securely stream files from private storage after
 * verifying the user's authorization to access the file.
 *
 * HOW IT FITS IN THE APP:
 * This controller provides the endpoint for all attachment
 * download links shown in ticket threads and create forms.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * We use a specialized Controller instead of a Livewire component
 * because Livewire is designed for interactive UI updates and
 * returns JSON, not file streams. Controllers allow us to
 * return a real HTTP StreamedResponse with the correct
 * download headers.
 * ============================================================
 */

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AttachmentController
{
    /**
     * Serve a private attachment file as a streamed download.
     */
    public function download(Attachment $attachment): StreamedResponse
    {
        // Authorize using policy: This calls AttachmentPolicy::view().
        // If the user cannot see the ticket, they cannot download its attachments.
        // Gate::authorize() throws a 403 automatically on failure.
        Gate::authorize('view', $attachment);

        // The DB record exists but the file might have been manually deleted.
        // Always check before serving to avoid internal server errors.
        abort_unless(Storage::disk($attachment->disk)->exists($attachment->path), 404, 'File not found on disk.');

        // download() streams the file through Laravel with the original filename as the download name.
        // The real storage path is never exposed to the browser.
        return Storage::disk($attachment->disk)
            ->download(
                $attachment->path,
                $attachment->original_name,
                ['Content-Type' => $attachment->mime_type]
            );
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Livewire/Requester/TicketCreateForm.php]
 * WHY: Now that we have the Action to store and the Controller
 * to download, we can update the UI components to support uploads.
 * ============================================================
 */
