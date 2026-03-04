<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: StoreAttachmentAction.php
 * LAYER: Action
 * ============================================================
 *
 * WHAT IS THIS?
 * Handles the secure storage and database recording of file
 * uploads related to helpdesk tickets or messages.
 *
 * WHY DOES IT EXIST?
 * To ensure a single, consistent way to process file uploads,
 * applying validation and storing files in private storage.
 *
 * HOW IT FITS IN THE APP:
 * This action is called from Livewire components (Requester OR Agent)
 * when a user attaches files to a new ticket or a reply.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * We use the 'private' disk for storage, which means the files
 * are not directly accessible via a URL. This ensures that
 * only authorized users can download them through our controller.
 * ============================================================
 */

namespace App\Actions;

use App\Models\Attachment;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class StoreAttachmentAction
{
    /**
     * Handle file validation, storage, and record creation.
     *
     * @throws ValidationException
     */
    public function execute(
        Ticket $ticket,
        ?TicketMessage $message,
        UploadedFile $file
    ): Attachment {
        // Validate file properties.
        // We validate in the Action not just in the Livewire component — defense in depth.
        Validator::make(['file' => $file], [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt,zip',
            ],
        ])->validate();

        // Generate a safe storage path.
        // store() on the private disk means the file goes to storage/app/private/attachments/{ticket_id}/
        // It has NO public URL. The only way to access it is through our AttachmentController which checks policy.
        $path = $file->store(
            'attachments/'.$ticket->id,
            'private'
        );

        // Create the Attachment record in the database.
        return Attachment::query()->create([
            'ticket_id' => $ticket->id,
            'message_id' => $message?->id,
            'disk' => 'private',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'size' => $file->getSize(),
        ]);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [app/Http/Controllers/AttachmentController.php]
 * WHY: After storing the file securely, we need a controller that
 * authorized users can use to download it.
 * ============================================================
 */
