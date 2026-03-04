<?php

declare(strict_types=1);

/**
 * ============================================================
 * FILE: AttachmentDownloadTest.php
 * LAYER: Feature Test
 * ============================================================
 *
 * WHAT IS THIS?
 * Verification suite for the file download security logic.
 *
 * WHY DOES IT EXIST?
 * To ensure that private attachments are only accessible by
 * legitimate users (the owner of the ticket or an agent).
 *
 * HOW IT FITS IN THE APP:
 * Tests the interaction between the AttachmentController,
 * the AttachmentPolicy, and the Storage facade.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * `assertOk()` confirms the file was served. In a real browser,
 * the StreamedResponse headers would trigger a download prompt.
 * ============================================================
 */

namespace Tests\Feature\Attachments;

use App\Actions\CreateTicketAction;
use App\Actions\CreateTicketData;
use App\Actions\StoreAttachmentAction;
use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    // Prevent real files from being written during tests.
    Storage::fake('private');
});

it('requester can download attachment on their own ticket', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);

    // 1. Create a ticket for a requester.
    $ticket = resolve(CreateTicketAction::class)->execute(
        $requester,
        new CreateTicketData('My Ticket', 'My ticket description.', null)
    );

    // 2. Add an attachment.
    $file = UploadedFile::fake()->create('mine.pdf', 100, 'application/pdf');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    // 3. Try to download as the owner.
    $this->actingAs($requester);
    $response = $this->get(route('attachments.download', $attachment));

    // 4. Assert download allowed.
    $response->assertOk();
    $response->assertHeader('Content-Disposition', 'attachment; filename=mine.pdf');
});

it('requester cannot download attachment on another users ticket', function (): void {
    /** @var User $owner */
    $owner = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute($owner, new CreateTicketData('Locked', 'No touchy.', null));

    // Create attachment for the owner.
    $file = UploadedFile::fake()->create('private.pdf', 50, 'application/pdf');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    /** @var User $stranger */
    $stranger = User::factory()->create(['role' => UserRole::Requester]);

    // 1. Try to download as another requester.
    $this->actingAs($stranger);
    $response = $this->get(route('attachments.download', $attachment));

    // 2. Assert forbidden (403).
    // This is the critical security test from HLD Module B. must be 403, not 404.
    $response->assertForbidden();
});

it('agent can download attachment on any ticket', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute($requester, new CreateTicketData('Help', 'Description.', null));

    $file = UploadedFile::fake()->create('log.txt', 20, 'text/plain');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    /** @var User $agent */
    $agent = User::factory()->create(['role' => UserRole::Agent]);

    // 1. Try to download as an agent.
    $this->actingAs($agent);
    $response = $this->get(route('attachments.download', $attachment));

    // 2. Assert allowed.
    $response->assertOk();
});

it('unauthenticated user cannot download any attachment', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute($requester, new CreateTicketData('Anonymous', 'Info.', null));

    $file = UploadedFile::fake()->create('test.jpg', 150, 'image/jpeg');
    $attachment = resolve(StoreAttachmentAction::class)->execute($ticket, null, $file);

    // 1. Try to download without authentication.
    $response = $this->get(route('attachments.download', $attachment));

    // 2. Assert redirect to login.
    $response->assertRedirect('/login');
});

it('returns 404 if file missing from disk but record exists', function (): void {
    /** @var User $requester */
    $requester = User::factory()->create(['role' => UserRole::Requester]);
    $ticket = resolve(CreateTicketAction::class)->execute($requester, new CreateTicketData('Broken', 'Info.', null));

    // Create a DB record manually, WITHOUT writing the file to disk.
    /** @var Attachment $attachment */
    $attachment = Attachment::query()->create([
        'ticket_id' => $ticket->id,
        'disk' => 'private',
        'path' => 'attachments/'.$ticket->id.'/ghost.pdf',
        'original_name' => 'ghost.pdf',
        'mime_type' => 'application/pdf',
        'size' => 1024,
    ]);

    // File is not created on disk (Storage::fake is empty).
    $this->actingAs($requester);
    $response = $this->get(route('attachments.download', $attachment));

    // Should return 404 because file is missing.
    $response->assertNotFound();
});

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * → [README.md]
 * WHY: After finishing the Day 7 implementation, you should
 * review the README to understand how to run the demo and tests.
 * ============================================================
 */
