# AgentDesk

## AgentDesk — High-Level Design (HLD)

```
Project: Helpdesk + AI Triage + Reply Assist
Stack: Laravel + Livewire + Laravel AI SDK
Base repo: nunomaduro/laravel-starter-kit (GitHub) use this as starter kit
Duration: 15 days (intern evaluation)
```
## 0) What this HLD is

```
This HLD is the single source of truth. It is written to be:
```
## • Unambiguous (clear triggers, responsibilities, outputs)

## • Demo-verifiable (every requirement has a “how to verify” step)

## • Comparable across interns (same checklist and quality gates)

## 1) Glossary

## • : A promised time target for support work. In this

```
project it means:
```
```
Service Level Agreement (SLA)
```
## ◦ : “A support agent should reply to a new ticket

```
within X hours.”
```
```
First response time target
```
## ◦ Resolution time target : “A ticket should be resolved within Y hours.”

```
The system checks these targets automatically on a schedule and alerts if
overdue.
```
## • : Quickly reviewing a new ticket to decide , , , and

```
whether it needs escalation.
```
```
Triage category prioritytags
```
## • : A saved canned response that support agents can insert into

```
replies.
```
```
Macro / Template
```
## • : A history of important actions (who changed status, who assigned a

```
ticket, who ran AI).
```
```
Audit log
```
## • : Internal articles/docs used to answer questions or

```
support replies.
```
```
Knowledge Base (KB)
```

- : A PHP function/class the AI agent can call to fetch structured data
    (e.g., KB snippets).

```
Tool (AI tool)
```
- : AI output that follows a strict schema (JSON-like) so we can
    store and validate it safely.

```
Structured output
```
```
UI wording: avoid “SLA” in screens—use and
.
```
```
Response time target Resolution
time target
```
## 2) Terminology clarification (VERY IMPORTANT)

The word can mean two different things. In this project we will use these
exact terms:

```
“agent”
```
1. **SupportAgent (Human)**
    A real person logged into the system with role. They triage tickets,
    assign tickets, and reply to requesters.

```
Agent
```
2. **AI Agent (Code)**
    A PHP class created using the Laravel AI SDK, such as ,
       ,.

```
TriageAgent
ReplyDraftAgent ThreadSummaryAgent
```
**Rule:** Only a **SupportAgent (Human)** can click buttons or perform UI actions.

AI Agents **never click anything** — they run only inside **queued Jobs**.

## 3) Starter kit constraints (mandatory)

You **must** build on nunomaduro/laravel-starter-kit and keep its strictness. (GitHub)

### 3.1 Runtime & tooling baseline

- Use and modern PHP features (enums, readonly DTOs, strict typing).
    ( )

#### PHP 8.4+

```
GitHub
```
- Tooling quality gates must remain strict and must pass in CI:

## ◦ Pint / Rector / Prettier via composer lint

## ◦ PHPStan strict via composer test:types

## ◦ Type coverage via composer test:type-coverage

## ◦ Full suite via composer test (GitHub)

### 3.2 Required quality gate (Definition of Done)


```
composer test must pass on a clean checkout. (GitHub)
```
Do **not** weaken rules to make it pass.

## 4) LLM provider constraint (mandatory): Groq Free

## API via Laravel AI SDK

### 4.1 Provider requirement

All LLM calls in this project must use through the

. ( )

**Groq Laravel AI SDK provider
support for Groq** Laravel

### 4.2 API key requirement

Developers must use a (configured via ) and document setup in
README (where to paste the key and which model they chose). Groq offers a
, but usage is capped by rate limits. ( )

**Groq API key** .env
**Free
Tier** Groq Community

### 4.3 Rate-limit aware design (required because free tier)

Because Groq Free Tier has quotas/rate limits, the app must implement:

- **Server-side rate limiting** on AI triggers (per user + per ticket)
- **Caching/dedup** using input_hash to avoid repeated identical AI runs
- Graceful UI errors when rate limits are hit (“Try again later” + keep draft state)

(Exact limits vary by account; interns must not hardcode limits—just build the
protection.) (GroqCloud)

### 4.4 Testing rule

Tests must **fake/mimic** AI calls so CI never consumes Groq quota.

## 5) Engineering standards (mandatory)

### 5.1 Enums (required)

Use backed enums for all finite states/constants:

- TicketStatus: New, Triaged, InProgress, Waiting, Resolved
- TicketPriority: Low, Medium, High, Urgent
- TicketMessageType: Public, Internal


- AiRunType: Triage, ReplyDraft, ThreadSummary
- AiRunStatus: Queued, Running, Succeeded, Failed
- UserRole: Admin, Agent, Requester

### 5.2 DTOs (required)

Use immutable DTOs (prefer readonly) for:

- agent inputs/outputs ( , , ,
    )

```
TriageInput TriageResult ReplyDraftInput
ReplyDraftResult
```
- tool outputs (KbSnippetDTO)
- important service boundaries (avoid raw arrays crossing layers)

### 5.3 PHP 8.4+ best practices (required)

- strict types
- typed properties + return types
- constructor property promotion
- readonly DTOs
- enums + match where appropriate

### 5.4 Layering (required)

- **Livewire components:** UI state + validation + dispatch actions/jobs
- **Actions/Services:** business logic
- **Jobs:** async orchestration
- **Agents/Tools:** AI boundaries
- **Models:** persistence & relations only

## 6) Product goal

Build a lightweight helpdesk where:

- Requesters create tickets with attachments
- SupportAgents (Human) triage/assign/respond/resolve
- Admins configure categories, response/resolution targets (SLA), macros
- AI Agents (Code) accelerate triage + reply drafting ( ) using
    Groq via Laravel AI SDK ( )

```
human-approved
Laravel
```

## 7) Non-goals (explicit exclusions)

Do **not** implement:

- billing/subscriptions
- full mailbox ingestion
- auto-sending AI replies without human approval
- complex analytics dashboards
- OCR requirement (optional extra credit only)
- agent CRUD UI (create/edit/delete AI agents) — Phase 2 only

## 8) Roles & authorization (mandatory)

### 8.1 Roles

- **Requester:** create tickets, view own tickets, post public replies
- triage queue, assigned tickets, internal notes, status
    changes, run AI

```
SupportAgent (Human):
```
- **Admin:** all access + configuration + audit visibility

### 8.2 Mandatory policy rules (minimum)

- Requester can **only** view/reply to **their own** tickets.
- SupportAgent (Human) can view tickets:

## ◦ assigned to them, OR

## ◦ in triage queue (status = New and unassigned)

- Only SupportAgent/Admin can assign tickets and change status.
- Only SupportAgent/Admin can add internal notes.
- Only Admin can manage categories/targets/macros/audit screens.
    open another requester’s ticket URL while logged in as someone else
→ must deny.

**How to verify:**

## 9) Required CRUD (explicit)

Only implement CRUD for the following entities.

### 9.1 Requester CRUD


**Tickets**

- Create required
- Read required (list + detail)
- Update optional (recommended: no edit after create; use replies)
- Delete not required

**Public Replies**

- Create required
- Read required
- Update/Delete not required

**Attachments**

- Create required
- Download/read required (authorized)
- Delete optional (only while ticket is New)

### 9.2 SupportAgent operations (not full CRUD)

**Ticket operations**

- Read (triage queue + assigned list + detail)
- Update (assign, tags, category, priority, status)
- Create (internal notes + public replies)
- Delete not required

### 9.3 Admin CRUD

- **Categories:** full CRUD^
- **Macros/Templates:** full CRUD^
- **Response/Resolution Targets (SLA config):** Read/Update (single record)
- **Knowledge Base (KB) Articles:** full CRUD^ _(only if you include KB/RAG)_
- **AI Runs:** read-only (list + detail), edit/delete^

## 10) System context (components)

- Livewire UI (Requester portal, SupportAgent console, Admin config)
- Laravel app (policies/services/jobs)


- DB (tickets/messages/attachments/config/ai_runs/audit)
- Storage (private attachments)
- Queue worker (AI jobs, notification jobs)
- Scheduler (checks response/resolution targets)
- Laravel AI SDK (AI Agents + Tools + structured output; embeddings optional)
    (Laravel)
- Groq Free API as the LLM backend (rate-limited) (Groq Community)

## 11) Modules (intent + verification)

### Module A — Ticketing workflow (core)

**Workflow (enum):** New → Triaged → InProgress → Waiting → Resolved

**Thread:** Public replies + Internal notes (separate message types)

**Intent:** workflow logic + authorization + good modeling

requester cannot see internal notes; support agent can assign and change
status.

**Verify:**

### Module B — Attachments (security)

- private storage
- authorized downloads only

**Intent:** secure file access

**Verify:** unauthorized user cannot download.

### Module C — Response/Resolution targets (SLA) + Scheduler

- configured time targets:

## ◦ first response target (hours)

## ◦ resolution target (hours)

- scheduled job checks for overdue tickets and notifies

**Intent:** scheduler usage + time logic

**Verify:** seeded overdue ticket triggers overdue notification.

### Module D — Notifications

- database + email (Mailhog ok)


- events:

## ◦ assigned

## ◦ requester replied

## ◦ resolved

## ◦ overdue response/resolution

**Intent:** event-driven flows + queued delivery

**Verify:** show notification entries.

### Module E — Audit log

- log status/assignment changes, AI runs, config changes
- admin viewer

**Intent:** enterprise traceability

**Verify:** audit entries visible after actions.

## 12) Trigger model (how AI Agents run)

AI Agents are executed only through **system triggers** :

**SupportAgent (Human) action or system event → Livewire method → create
ai_runs row → dispatch Job → Job calls AI Agent (Code) → save output → UI
shows result**

### Trigger Map (must implement)

```
Trigger Source UI/System Trigger What the app does immediately Queue J
Requester (Human) Submits a new ticket Create ticket + create
(Triage, Queued)
```
```
ai_runs RunTick
```
```
SupportAgent (Human) Clicks Run Triage (optional rerun) Create ai_runs (Triage, Queued) RunTick
SupportAgent (Human) Clicks Generate Reply Create (ReplyDraft,
Queued)
```
```
ai_runs DraftTi
user_id
SupportAgent (Human) Clicks Summarize Thread (optional) Create (ThreadSummary,
Queued)
```
```
ai_runs Summari
d)
Scheduler (System) Cron tick for overdue checks Find overdue tickets + notify CheckOv
```

## 13) AI subsystem (fixed AI Agents; no agent CRUD)

### 13.1 MVP rule

- AI Agents are fixed and code-defined
- No UI to create/edit/delete AI Agents (Phase 2 can add settings later)

### 13.2 Required AI Agents (min 2; recommended 3)

Each AI Agent (Code) must have:

- single responsibility
- DTO input + DTO output
- structured output stored in DB
- provider configured to Groq via Laravel AI SDK (Laravel)

### AI Agent 1 — TriageAgent (required)

category suggestion, priority suggestion, summary, tags, clarifying
questions, escalation flag

**Output:**

**Verify:** ticket shows triage result; support agent can apply suggested fields.

### AI Agent 2 — ReplyDraftAgent (required)

**Must use tool:** KB snippet retrieval tool for grounding

**Output:** reply draft + next steps + risk flags

**Verify:** streaming/progress UI + draft saved.

### AI Agent 3 — ThreadSummaryAgent (optional / extra credit)

**Output:** thread summary + recommended next action

**Verify:** summary stored and displayed.

### 13.3 Required Tool (min 1)

### SearchKnowledgeBaseTool (required)

Input: query + scope

Output: list of KB snippets (DTOs)

**Intent:** deterministic grounding + tool usage pattern

**Verify:** show retrieved snippets and confirm ReplyDraftAgent used them.


### 13.4 Grounding requirement (two levels)

- Preferred: embeddings + vector store over KB articles
- Acceptable: keyword search behind the Tool (Scout/Meilisearch or DB fulltext)

## 14) AI run persistence (mandatory)

Create ai_runs table. Every AI invocation must create a record:

- run_type (enum), status (enum), initiated_by_user_id
- input_hash (for caching/dedup)
- output_json + provider/model metadata
- error_message + timestamps

**Verify:** admin can view AI runs list and open details.

## 15) Queues & Jobs (mandatory)

Required jobs:

- RunTicketTriageJob(ticket_id)
- DraftTicketReplyJob(ticket_id, initiated_by_user_id)
- CheckOverdueTargetsJob() (scheduled)

Optional:

- SummarizeTicketThreadJob(ticket_id)
- IndexKnowledgeBaseJob(kb_article_id) (if embeddings/RAG)

## 16) Livewire UI requirements (mandatory)

Requester:

- TicketCreateForm (validation + uploads)
- MyTicketsTable (filters + pagination)
- TicketDetail (thread + reply)

SupportAgent:

- TriageQueue (bulk assign/tag/priority)
- TicketDetail (status/assignee; internal/public tabs)
- AiPanel (run triage + generate reply + show progress/stream)


Admin:

- Categories CRUD
- Targets (SLA) settings (update)
- Macros CRUD
- KB CRUD (if used)
- Audit log viewer
- AI runs viewer (read-only)

## 17) Streaming/progress UX (mandatory)

Reply generation must show either:

- true streaming output, OR
- step progress: Retrieving → Drafting → Ready (with partial updates)

**Verify:** click Generate Reply and see progress live.

## 18) Security & performance (mandatory)

- Rate limit ticket create, reply post, AI triggers (important with Groq free tier)
    (GroqCloud)
- Cache triage results and KB retrieval by input_hash
- Avoid N+1 queries (eager load relations)

## 19) Testing + strict tooling gates (mandatory)

### Tests (minimum)

- 6 feature tests (RBAC, workflow, attachment auth, notifications)
- 2 Livewire tests (create ticket, triage actions)
- AI faked/mocked (no external calls → no Groq quota usage)

### Tooling gates (must pass)

- composer lint (Pint + Rector + Prettier) (GitHub)
- composer test:types (PHPStan strict) (GitHub)
- composer test:type-coverage (type coverage) (GitHub)


- composer test (all) (GitHub)

## 20) Definition of Done (DoD)

### Functional

```
Roles + policies implemented; no auth gaps
Ticket workflow + assignment + internal/public thread works
Secure attachments with authorized downloads
Overdue response/resolution checks run via scheduler and notify
Admin config screens (categories, targets, macros) exist
Audit log and AI runs viewer exist
CSV export of tickets exists
```
### AI (Groq required)

```
Fixed code-defined AI Agents: + (summary
optional)
```
```
TriageAgent ReplyDraftAgent
```
```
Tool: SearchKnowledgeBaseTool
All AI calls are queued jobs
Reply generation shows streaming/progress UI
AI runs stored in ai_runs with status + output_json
LLM provider used is via Laravel AI SDK (document model used in
README) ( )
```
```
Groq
Laravel
```
### Quality/Tooling

```
composer test passes on fresh clone (GitHub)
Meets minimum test counts and uses AI fakes
Enums + DTOs used across domain and AI boundaries
README includes setup, Groq env instructions, scripts, queue/scheduler, and
demo script
```
## 21) Demo script (2–3 minutes)

1. Requester creates ticket + attachment


2. SupportAgent opens triage queue → runs triage → shows structured results
    applied
3. SupportAgent generates reply → streaming/progress → edits and sends
4. Requester sees reply + notification
5. Run scheduler on seeded overdue ticket → show overdue notification
6. Admin shows audit log + AI runs list