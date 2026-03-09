# AgentDesk — AI-Powered Helpdesk

AgentDesk is a modern, high-performance helpdesk application built with **Laravel 12**, **Livewire Volt**, and the **Laravel AI SDK**. It leverages **Groq** to provide near-instant AI triage and reply drafting for support tickets.

## 🚀 Quick Start

### 1. Prerequisites
- **PHP 8.4+**
- **Composer** & **Bun/NPM**
- **SQLite** (default setup)
- **Groq API Key** (Get one for free at [groq.com](https://console.groq.com/keys))

### 2. Installation
```bash
# Clone the repository
git clone <repo-url> agentdesk
cd agentdesk

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 3. Database & Seeding
```bash
# Run migrations and seed the initial data (Admins, Agents, Categories)
php artisan migrate:fresh --seed
```

### 4. Groq Configuration
Add your Groq API key to `.env`:
```env
AI_DEFAULT_PROVIDER=groq
GROQ_API_KEY=gsk_...
```

### 5. Start Development Servers
You need three terminals running:
```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Vite (styling/JS)
npm run dev

# Terminal 3: Queue worker (CRITICAL for AI runs)
php artisan queue:listen
```

---

## 🛠 Available Scripts

- `composer test` — Run the full suite (Pest, PHPStan Max, Pint, Rector)
- `php artisan scheduler:work` — Run the background scheduler (for SLA checks)
- `php artisan tinker` — Execute PHP directly in the app context

---

## 🤖 AI Subsystem (Laravel AI SDK)

AgentDesk uses structured output and AI agents to streamline support:

1. **TriageAgent**: Automatically categorizes, prioritizes, and summarizes incoming tickets. Runs immediately on ticket creation.
2. **ReplyDraftAgent**: Generates high-quality drafts for support agents, grounded by KB snippets via the `SearchKnowledgeBaseTool`.

**Note:** All AI calls are faked in the test suite to preserve your Groq quota.

---

## ⏰ Background Jobs & Scheduler

- `RunTicketTriageJob`: Orhcestrates the TriageAgent run.
- `DraftTicketReplyJob`: Orchestrates the ReplyDraftAgent run.
- `CheckOverdueTargetsJob`: Runs hourly (via `php artisan schedule:run`) to check for tickets breaching response/resolution targets.

---

## 📝 Demo/Evaluation Script (2 mins)

Follow these steps to verify the core functionality:

1. **Register as a Requester**: Go to `/register` and create a new ticket with an attachment.
2. **AI Triage**: Log in as an Agent (`agent@example.com` / `password`). Go to **Triage Queue**. You will see the ticket has been automatically categorized and prioritized by AI.
3. **Draft Reply**: Open the ticket. Click **Generate AI Reply** in the AI Panel. Watch the progress bar as it retrieves KB snippets and drafts a response.
4. **Notification**: Mark the ticket as assigned. The new assignee will get a notification in their **Notification Bell**.
5. **SLA Breach**: Run `php artisan app:check-sla` (custom command if available, or just wait for/trigger the job) to see overdue notifications trigger for old tickets.
6. **Admin Audit**: Log in as Admin (`admin@example.com` / `password`). View the **Audit Log** to see every status change and **Export Tickets** to CSV.

---
