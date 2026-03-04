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

## Day 6 — Attachments

[To be filled in after Day 5 is complete]

## Day 7 — Scheduler + Notifications

[To be filled in after Day 6 is complete]

## Day 8 — AI Subsystem: TriageAgent

[To be filled in after Day 7 is complete]

## Day 9 — AI Subsystem: ReplyDraftAgent + KB Tool

[To be filled in after Day 8 is complete]

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
