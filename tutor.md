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

[To be filled in after Day 2 is complete]

## Day 4 — Requester Ticket Flow

[To be filled in after Day 3 is complete]

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
