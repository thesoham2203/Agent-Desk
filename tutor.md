# AgentDesk — Learning Guide

## How to use this file

This file is your day-by-day guide. Each day tells you:

- What you are building
- Which files to read (in order)
- What Laravel concept each file teaches
- What to run in terminal to verify your work

---

## Day 2 — Database Foundation

**Goal:** Understand how Laravel models data before writing any UI.

### What you will build:

- Enums (type-safe constants)
- Migrations (database table definitions)
- Models (PHP classes that represent database rows)
- DTOs (data transfer objects for AI boundaries)
- Seeders (realistic fake data for development)

### Files to read in order (follow the linked list in each file):

1. [app/Enums/TicketStatus.php](app/Enums/TicketStatus.php) → Learn: what backed enums are
2. [app/Enums/TicketPriority.php](app/Enums/TicketPriority.php) → Learn: why enums replace magic strings
3. [app/Enums/TicketMessageType.php](app/Enums/TicketMessageType.php)
4. [app/Enums/AiRunType.php](app/Enums/AiRunType.php)
5. [app/Enums/AiRunStatus.php](app/Enums/AiRunStatus.php)
6. [app/Enums/UserRole.php](app/Enums/UserRole.php)
7. [database/migrations/2026_03_02_000001_add_role_to_users_table.php](database/migrations/2026_03_02_000001_add_role_to_users_table.php) → Learn: migrations
8. [database/migrations/2026_03_02_000002_create_categories_table.php](database/migrations/2026_03_02_000002_create_categories_table.php)
9. [database/migrations/2026_03_02_000003_create_tickets_table.php](database/migrations/2026_03_02_000003_create_tickets_table.php) → Learn: foreign keys
10. [database/migrations/2026_03_02_000004_create_ticket_messages_table.php](database/migrations/2026_03_02_000004_create_ticket_messages_table.php)
11. [database/migrations/2026_03_02_000005_create_attachments_table.php](database/migrations/2026_03_02_000005_create_attachments_table.php)
12. [database/migrations/2026_03_02_000006_create_macros_table.php](database/migrations/2026_03_02_000006_create_macros_table.php)
13. [database/migrations/2026_03_02_000007_create_sla_configs_table.php](database/migrations/2026_03_02_000007_create_sla_configs_table.php)
14. [database/migrations/2026_03_02_000008_create_kb_articles_table.php](database/migrations/2026_03_02_000008_create_kb_articles_table.php)
15. [database/migrations/2026_03_02_000009_create_ai_runs_table.php](database/migrations/2026_03_02_000009_create_ai_runs_table.php)
16. [database/migrations/2026_03_02_000010_create_audit_logs_table.php](database/migrations/2026_03_02_000010_create_audit_logs_table.php)
17. [app/Models/User.php](app/Models/User.php) → Learn: Eloquent models + casts
18. [app/Models/Category.php](app/Models/Category.php)
19. [app/Models/Ticket.php](app/Models/Ticket.php) → Learn: relationships (belongsTo, hasMany)
20. [app/Models/TicketMessage.php](app/Models/TicketMessage.php)
21. [app/Models/Attachment.php](app/Models/Attachment.php)
22. [app/Models/Macro.php](app/Models/Macro.php)
23. [app/Models/SlaConfig.php](app/Models/SlaConfig.php)
24. [app/Models/KbArticle.php](app/Models/KbArticle.php)
25. [app/Models/AiRun.php](app/Models/AiRun.php)
26. [app/Models/AuditLog.php](app/Models/AuditLog.php)
27. [app/AI/DTOs/TriageInput.php](app/AI/DTOs/TriageInput.php) → Learn: readonly DTOs
28. [app/AI/DTOs/TriageResult.php](app/AI/DTOs/TriageResult.php)
29. [app/AI/DTOs/ReplyDraftInput.php](app/AI/DTOs/ReplyDraftInput.php)
30. [app/AI/DTOs/ReplyDraftResult.php](app/AI/DTOs/ReplyDraftResult.php)
31. [app/AI/DTOs/KbSnippetDTO.php](app/AI/DTOs/KbSnippetDTO.php)
32. [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php) → Learn: seeders + Faker

### Terminal commands to run after reading:

```bash
php artisan migrate:fresh --seed
php artisan tinker
# In tinker, try:
# \App\Models\Ticket::with('requester','messages')->first()
# \App\Models\User::first()->role->label()
```

### How to verify Day 2 is complete:

- php artisan migrate:fresh --seed runs with zero errors
- php artisan tinker → Ticket::count() returns 20
- Ticket::first()->status returns a TicketStatus enum (not a string)
- User::first()->role->label() returns a readable string
- composer test:types passes with no errors

---

## Day 3 — Roles & Policies

**Goal:** Understand how Laravel authorizes actions using Policies before writing any UI or API routes.

### What you will build:
- app/Policies/TicketPolicy.php
- app/Policies/TicketMessagePolicy.php
- app/Policies/AttachmentPolicy.php
- app/Policies/CategoryPolicy.php
- app/Policies/MacroPolicy.php
- app/Policies/SlaConfigPolicy.php
- app/Policies/KbArticlePolicy.php
- app/Policies/AiRunPolicy.php
- app/Policies/AuditLogPolicy.php
- app/Providers/AppServiceProvider.php
- tests/Feature/Policies/TicketPolicyTest.php
- tests/Feature/Policies/TicketMessagePolicyTest.php
- tests/Feature/Policies/AttachmentPolicyTest.php
- tests/Feature/Policies/AdminPolicyTest.php

### Files to read in order (follow the linked list in each file):

1. [app/Policies/TicketPolicy.php](app/Policies/TicketPolicy.php) → Learn: Laravel Policies
2. [app/Policies/TicketMessagePolicy.php](app/Policies/TicketMessagePolicy.php)
3. [app/Policies/AttachmentPolicy.php](app/Policies/AttachmentPolicy.php)
4. [app/Policies/CategoryPolicy.php](app/Policies/CategoryPolicy.php)
5. [app/Policies/MacroPolicy.php](app/Policies/MacroPolicy.php)
6. [app/Policies/SlaConfigPolicy.php](app/Policies/SlaConfigPolicy.php)
7. [app/Policies/KbArticlePolicy.php](app/Policies/KbArticlePolicy.php)
8. [app/Policies/AiRunPolicy.php](app/Policies/AiRunPolicy.php)
9. [app/Policies/AuditLogPolicy.php](app/Policies/AuditLogPolicy.php) → Learn: before() hooks
10. [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php) → Learn: policy registration
11. [tests/Feature/Policies/TicketPolicyTest.php](tests/Feature/Policies/TicketPolicyTest.php) → Learn: feature tests
12. [tests/Feature/Policies/TicketMessagePolicyTest.php](tests/Feature/Policies/TicketMessagePolicyTest.php)
13. [tests/Feature/Policies/AttachmentPolicyTest.php](tests/Feature/Policies/AttachmentPolicyTest.php)
14. [tests/Feature/Policies/AdminPolicyTest.php](tests/Feature/Policies/AdminPolicyTest.php)

### Terminal commands to run after reading:

```bash
php artisan test --compact tests/Feature/Policies/
composer test:types
```

### How to verify Day 3 is complete:

- All policy feature tests pass
- composer test:types passes with no errors
- A Requester trying to view another user's ticket → denied
- A SupportAgent can view unassigned New tickets
- An Admin can do everything

## Day 4 — Requester Ticket Flow

### Goal:
Build the complete requester experience — create tickets,
view their own ticket list, read thread, post replies.

### What you will build:
- app/Actions/CreateTicketAction.php
- app/Actions/PostReplyAction.php
- app/Livewire/Requester/TicketCreateForm.php
- app/Livewire/Requester/MyTicketsTable.php
- app/Livewire/Requester/TicketDetail.php
- resources/views/livewire/requester/ticket-create-form.blade.php
- resources/views/livewire/requester/my-tickets-table.blade.php
- resources/views/livewire/requester/ticket-detail.blade.php
- routes/web.php (requester route group)

### The core Livewire concepts you will learn:
- public properties = state (wire:model binds them to inputs)
- #[Validate] = validation rules as PHP attributes
- mount($id) = runs once when component loads (like a constructor)
- wire:submit = calls a PHP method when form is submitted
- wire:click = calls a PHP method when button is clicked
- wire:poll.3s = re-renders component every 3 seconds (used later for AI)
- $this->authorize() = checks the Policy before doing anything
- dispatch() = fires an event to other Livewire components

### Files to read in order:
1. app/Actions/CreateTicketAction.php
2. app/Actions/PostReplyAction.php
3. app/Livewire/Requester/TicketCreateForm.php
4. resources/views/livewire/requester/ticket-create-form.blade.php
5. app/Livewire/Requester/MyTicketsTable.php
6. resources/views/livewire/requester/my-tickets-table.blade.php
7. app/Livewire/Requester/TicketDetail.php
8. resources/views/livewire/requester/ticket-detail.blade.php
9. routes/web.php

### Terminal commands to run:
```bash
php artisan migrate:fresh --seed
php artisan serve
# Log in as requester@agentdesk.test / password
# Create a ticket, view it, post a reply
composer test:types
composer test
```

### How to verify Day 4 is complete:
- Requester can create a ticket with a title and body
- Ticket appears in MyTicketsTable immediately after creation
- Requester can open the ticket and see the thread
- Requester can post a public reply
- Requester CANNOT see internal notes (verified by policy)
- Requester CANNOT see another user's ticket (verified by policy)
- composer test:types passes
- All Day 4 feature tests pass

## Day 5 — SupportAgent Operations

[To be filled in after Day 4 is complete]

## Day 6 — SupportAgent Operations

### Goal:
Build the complete SupportAgent experience — triage queue,
ticket management, internal notes, public replies, status
and priority changes, assignment.

### What you will build:
- app/Actions/AssignTicketAction.php
- app/Actions/ChangeTicketStatusAction.php
- app/Actions/AddInternalNoteAction.php
- app/Livewire/Agent/TriageQueue.php
- app/Livewire/Agent/TicketDetail.php
- resources/views/livewire/agent/triage-queue.blade.php
- resources/views/livewire/agent/ticket-detail.blade.php
- routes/web.php (agent route group added)

### Core concepts you will learn:
- How the same model (Ticket) can have different views
  for different roles (Requester vs Agent TicketDetail)
- How Actions keep components thin
- How internal notes are hidden from requesters at query level
- How status transitions are enforced in business logic

### Files to read in order:
1. app/Actions/AssignTicketAction.php
2. app/Actions/ChangeTicketStatusAction.php
3. app/Actions/AddInternalNoteAction.php
4. app/Livewire/Agent/TriageQueue.php
5. resources/views/livewire/agent/triage-queue.blade.php
6. app/Livewire/Agent/TicketDetail.php
7. resources/views/livewire/agent/ticket-detail.blade.php
8. routes/web.php

### Terminal commands to run:
```bash
php artisan migrate:fresh --seed
php artisan serve
# Log in as agent@agentdesk.test / password
# Visit /agent/queue
# Assign a ticket, change status, add internal note
composer test:types
composer test
```

### How to verify Day 6 is complete:
- Agent sees triage queue with unassigned New tickets
- Agent can assign a ticket to themselves
- Agent can change ticket status
- Agent can add an internal note (not visible to requester)
- Agent can post a public reply
- Requester CANNOT access /agent/* routes
- composer test:types passes
- All Day 6 feature tests pass

## Day 7 — Attachments & Private Storage

### Goal:
Implement secure file upload and download. Files are stored
on the private disk and can only be downloaded by authorized users.
No file has a public URL — all downloads go through a policy check.

### What you will build:
- app/Actions/StoreAttachmentAction.php
- app/Http/Controllers/AttachmentController.php
- Updates to TicketCreateForm (add file upload)
- Updates to Requester/TicketDetail (add file upload on reply)
- Updates to Agent/TicketDetail (add file upload on reply)
- routes/web.php (attachment download route)
- tests/Feature/Attachments/AttachmentUploadTest.php
- tests/Feature/Attachments/AttachmentDownloadTest.php

### Core concepts you will learn:
- Storage::disk('private') vs Storage::disk('public')
  Private = not web accessible, Public = has a URL
- response()->download() vs response()->file()
  Streams files from private storage through Laravel
- Why file downloads need a Controller not a Livewire component
  (Livewire returns JSON updates, not file streams)
- Storage::fake('private') in tests
  Prevents real files being written during tests

### Files to read in order:
1. app/Actions/StoreAttachmentAction.php
2. app/Http/Controllers/AttachmentController.php
3. app/Livewire/Requester/TicketCreateForm.php (updated)
4. app/Livewire/Requester/TicketDetail.php (updated)
5. app/Livewire/Agent/TicketDetail.php (updated)
6. routes/web.php
7. tests/Feature/Attachments/AttachmentUploadTest.php
8. tests/Feature/Attachments/AttachmentDownloadTest.php

### Terminal commands to run:
```bash
php artisan migrate:fresh --seed
php artisan serve
# Log in as requester@agentdesk.test
# Create a ticket with a file attachment
# Download the attachment — should work
# Log in as different requester — try to download → 403
composer test:types
composer test
```

### How to verify Day 7 is complete:
- File uploads work on ticket create form
- Files are stored in storage/app/private (NOT storage/app/public)
- Downloading own attachment works
- Downloading another user's attachment returns 403
- Storage::fake() used in all attachment tests
- composer test:types passes

## Day 8 — AI Subsystem

**Goal:** Build the complete AI pipeline — tool, agents, jobs, polling UI.
Agents call Groq API. Jobs run agents async. UI polls for results.

### What you will build:
- app/AI/Tools/SearchKnowledgeBaseTool.php
- app/AI/Agents/TriageAgent.php
- app/AI/Agents/ReplyDraftAgent.php
- app/Jobs/RunTicketTriageJob.php
- app/Jobs/DraftTicketReplyJob.php
- app/Livewire/Agent/AiPanel.php
- resources/views/livewire/agent/ai-panel.blade.php
- Update app/Actions/CreateTicketAction.php (uncomment job dispatch)
- Update routes/web.php (add AI trigger routes)
- tests/Feature/AI/TriageAgentTest.php
- tests/Feature/AI/ReplyDraftAgentTest.php
- tests/Feature/AI/AiJobTest.php

### The async flow (memorize this):

```text
Agent clicks "Run Triage"
      ↓
AiPanel::runTriage() called
      ↓
AiRun::create(['status' => 'queued'])  ← instant DB write
      ↓
RunTicketTriageJob::dispatch($aiRun->id)  ← instant queue push
      ↓
UI immediately shows "Queued" status
      ↓
[BACKGROUND: queue worker picks up job]
      ↓
Job updates ai_runs status → 'running'
      ↓
Job calls TriageAgent->handle(TriageInput)
      ↓
TriageAgent calls Groq API
      ↓
Job saves result → ai_runs status = 'succeeded', output_json filled
      ↓
wire:poll.2s on AiPanel detects status change
      ↓
UI renders the triage result
```

### Files to read in order:
1. app/AI/Tools/SearchKnowledgeBaseTool.php
2. app/AI/Agents/TriageAgent.php
3. app/AI/Agents/ReplyDraftAgent.php
4. app/Jobs/RunTicketTriageJob.php
5. app/Jobs/DraftTicketReplyJob.php
6. app/Livewire/Agent/AiPanel.php
7. resources/views/livewire/agent/ai-panel.blade.php

### Terminal commands to run:
```bash
php artisan migrate:fresh --seed
# In a SEPARATE terminal window:
php artisan queue:work --tries=3
# In your browser:
php artisan serve
# Log in as agent, open a ticket, run triage, watch status change
composer test:types
composer test
```

### .env values needed:
```text
GROQ_API_KEY=your-key-here
GROQ_MODEL=llama3-8b-8192
QUEUE_CONNECTION=database
```


## Day 9 — Scheduler & Notifications

### Goal:
Build SLA overdue detection and notification system.
A scheduled job finds overdue tickets and notifies
the assigned agent and admin automatically.

### What you will build:
- app/Notifications/TicketAssignedNotification.php
- app/Notifications/RequesterRepliedNotification.php
- app/Notifications/TicketResolvedNotification.php
- app/Notifications/TicketOverdueNotification.php
- app/Jobs/CheckOverdueTargetsJob.php
- Update routes/console.php (schedule registration)
- Update app/Actions/AssignTicketAction.php (send notification)
- Update app/Actions/PostReplyAction.php (send notification)
- Update app/Actions/ChangeTicketStatusAction.php (send notification)
- tests/Feature/Notifications/NotificationTest.php
- tests/Feature/Scheduler/CheckOverdueTargetsJobTest.php

### Core concepts you will learn:
- Laravel Notifications: one class, multiple channels
  (database + mail from same notification class)
- Notifiable trait: what $user->notify() does internally
- Database notifications: stored in notifications table,
  read by UI to show notification bell/count
- Laravel Scheduler: define schedule in code not cron
  One cron entry: * * * * * php artisan schedule:run
  Laravel handles the rest based on your schedule definitions
- Why schedule:run every minute: Laravel checks internally
  if each scheduled job should run at that moment

### Files to read in order:
1. app/Notifications/TicketAssignedNotification.php
2. app/Notifications/RequesterRepliedNotification.php
3. app/Notifications/TicketResolvedNotification.php
4. app/Notifications/TicketOverdueNotification.php
5. app/Jobs/CheckOverdueTargetsJob.php
6. routes/console.php

### Terminal commands to run:
```bash
php artisan notifications:table
php artisan migrate
php artisan migrate:fresh --seed
# Test scheduler manually (runs due jobs immediately):
php artisan schedule:run
# Or run just the overdue job directly:
php artisan tinker
>>> App\Jobs\CheckOverdueTargetsJob::dispatchSync()
composer test:types
composer test
```

### How to verify Day 9 is complete:
- php artisan schedule:run triggers CheckOverdueTargetsJob
- 3 seeded overdue tickets each generate a notification
- Notifications appear in the notifications table
- composer test:types passes
- All notification tests pass

## Day 10 — Streaming UI + Rate Limiting

[To be filled in after Day 9 is complete]

## Day 11-12 — Admin Screens + Audit Log

[To be filled in after Day 10 is complete]

## Day 13-14 — Tests + PHPStan

[To be filled in after Day 12 is complete]

## Day 15 — Demo Prep

[To be filled in after Day 14 is complete]

---

_This file is updated at the start of each day with
detailed instructions for that day's work._
