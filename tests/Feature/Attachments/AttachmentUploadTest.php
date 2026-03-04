<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AttachmentUploadTest.php
 * LAYER: Feature Test
 * ============================================================
 *
 * WHAT IS THIS?
 * Verification suite for the file upload business logic.
 *
 * WHY DOES IT EXIST?
 * To ensure that files are properly validated, securely stored
 * on the private disk, and correctly recorded in the database.
 *
 * HOW IT FITS IN THE APP:
 * Tests the interaction between Livewire components, the
 * StoreAttachmentAction, and the Storage facade.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * `Storage::fake('private')` is used to prevent real files from
 * being written to the disk during tests. It provides an
 * in-memory filesystem that we can easily assert against.
 * ============================================================
 */

namespace Tests\Feature\Attachments;

use App\Actions\CreateTicketAction;
use App\Actions\CreateTicketData;
use App\Actions\StoreAttachmentAction;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Prevent real files from being written during tests.
    Storage::fake('private');
});

it('requester can upload attachment when creating ticket', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    // 1. Create ticket via Action.
    $ticket = resolve(CreateTicketAction::class)->execute(
        $requester,
        new CreateTicketData('Test Title', 'Test Body description here.', null)
    );

    // 2. Upload file via Action.
    $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    // 3. Assertions.
    // Verify DB entry.
    $this->assertDatabaseHas('attachments', [
        'id' => $attachment->id,
        'original_name' => 'test.pdf',
        'ticket_id' => $ticket->id,
    ]);

    // Verify file exists on the private disk.
    // Storage::fake() intercepts these calls and checks the in-memory store.
    Storage::disk('private')->assertExists($attachment->path);
});

it('attachment is stored on private disk not public', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute(
        $requester,
        new CreateTicketData('Disk Test', 'Body for disk testing purposes.', null)
    );

    $file = UploadedFile::fake()->create('secure.png', 50, 'image/png');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    // Explicitly verify the file did NOT end up on the public disk — this is the security test.
    $this->assertEquals('private', $attachment->disk);
    Storage::disk('public')->assertMissing($attachment->path);
    Storage::disk('private')->assertExists($attachment->path);
});

it('rejects files exceeding 10MB', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute(
        $requester,
        new CreateTicketData('Size Test', 'Body for size testing purposes.', null)
    );

    // 11MB file.
    $file = UploadedFile::fake()->create('huge.pdf', 11000, 'application/pdf');

    // Validation defined in StoreAttachmentAction should trigger.
    $this->expectException(ValidationException::class);
    resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);
});

it('rejects disallowed file types', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute(
        $requester,
        new CreateTicketData('Type Test', 'Body for type testing purposes.', null)
    );

    // Dangerous file extension.
    $file = UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload');

    // Validation should stop this.
    $this->expectException(ValidationException::class);
    resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [tests/Feature/Attachments/AttachmentDownloadTest.php]
 * WHY: After verifying that files can be securely uploaded, we
 * must verify that only authorized users can download them.
 * ============================================================
 */
