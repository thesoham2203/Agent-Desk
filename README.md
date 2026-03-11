# AgentDesk — AI-Powered Helpdesk

AgentDesk is a high-performance, AI-empowered helpdesk application built with **Laravel 12**, **Livewire Volt**, and the **Laravel AI SDK**. It leverages the extremely fast **Groq API** to provide near-instant AI triage and reply drafting for support tickets, enhancing agent productivity without replacing the human touch.

---

## 🌟 Key Features

### For Support Agents
- **Triage Queue**: Instant view of new, unassigned tickets needing attention.
- **My Tickets**: Personalized dashboard for tracking and managing your assigned workload.
- **AI Panel**: Real-time AI-powered triage results and professional reply drafting grounded in your Knowledge Base.
- **Internal Notes**: Private collaboration space completely hidden from customers.

### For Administrators
- **Executive Dashboard**: High-level system statistics and activity overview.
- **Audit Logs**: Comprehensive trace of all status changes, assignments, and structural updates.
- **AI Run Management**: Transparency into all AI executions, including input hashes and agent attribution.
- **Dynamic Configuration**: Hot-swap Categories, SLA Targets, Macros, and Knowledge Base articles via a rich UI.

### For Requesters
- **Intuitive Support Portal**: Simple ticket creation and real-time thread tracking.
- **Secure Attachments**: End-to-end private file management for sensitive data.

---

## 🚀 Quick Start & Installation

Follow these instructions to set up the project on your local machine after a fresh clone.

### 1. Prerequisites
- **PHP 8.4+** 
- **Composer**
- **Node.js & NPM** (or Bun)
- **SQLite** (Default database connection, zero-config)
- **Groq API Key** (Get one for free at [console.groq.com](https://console.groq.com/keys))

### 2. Fresh Clone Setup
```bash
# Clone the repository
git clone <repo-url> agentdesk
cd agentdesk

# Install PHP and Node dependencies
composer install
npm install

# Setup environment variables
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database & Seeding
AgentDesk comes with a rich seeder that provisions simulated Admins, Agents, Requesters, Categories, SLA Configs, Macros, Knowledge Base articles, and sample tickets.

```bash
# Create the SQLite database file
New-Item .\database\database.sqlite -ItemType File
# Run migrations and seed the initial data
php artisan migrate:fresh --seed
```

### 4. Groq Configuration
To enable the AI subsystem, you must provide a valid Groq API key. Open your `.env` file and append/update the following:

```env
# Tell the Laravel AI SDK to use Groq
AI_DEFAULT_PROVIDER=groq

# Your actual Groq API Key
GROQ_API_KEY=gsk_your_groq_api_key_here

# The model to use (recommended for agent tasks)
GROQ_MODEL=llama-3.3-70b-versatile
```

---

## 🏃‍♂️ Running the Application

AgentDesk relies on background queue workers for AI tasks and a scheduler for SLA monitoring. **To fully run the application locally, you should run these processes in separate terminal tabs:**

### Terminal 1: Web Server
```bash
php artisan serve
# or if you have Laravel Herd Installed then
add the project to herd and run agestdesk.test in your browser
```

### Terminal 2: Frontend Assets (Vite)
```bash
npm run dev
##  or if you have Laravel Herd Installed then
npm run build
```

### Terminal 3: Queue Worker (Crucial for AI)
AI operations (Triage & Reply Drafting) are dispatched to the database queue to keep the UI snappy. You **must** run the queue worker to process them.
```bash
php artisan queue:work --tries=3
```

### Terminal 4: Scheduler (Optional but recommended for SLA)
The scheduler checks for tickets breaching their Service Level Agreement (SLA) response and resolution targets.
```bash
php artisan schedule:work
```
*(Alternatively, you can manually trigger the specific job via Tinker: `php artisan tinker` -> `App\Jobs\CheckOverdueTargetsJob::dispatchSync();`)*

---

## 🔐 Authentication & Roles

AgentDesk uses Laravel Breeze with custom role-based access control. The seeder provisions these accounts:

| Role       | Email                     | Password |
|------------|---------------------------|----------|
| Admin      | admin@agentdesk.test      | password |
| Agent      | agent@agentdesk.test      | password |
| Requester  | requester@agentdesk.test  | password |

## 🛠 Testing & Quality Tools

AgentDesk strictly enforces code quality and type safety.

```bash
# Run the complete test suite (Pest feature & unit tests)
composer test
or
composer test --parallel (for faster execution)

# Run PHPStan (Strict Type Checking - Level Max)
composer test:types 
or 
composer test:types --parallel (for faster execution)

# Run Type Coverage Analysis
composer test:type-coverage

# Run Code Formatting (Pint, Rector)
composer lint
```
*Note: AI API calls are completely mocked/faked in the test suite, meaning running the tests will **not** consume your Groq quota.*

---

## 🤖 AI Subsystem (Laravel AI SDK)

AgentDesk orchestrates AI operations securely using background jobs and strictly typed Data Transfer Objects (DTOs):

1. **TriageAgent (`app/AI/Agents/TriageAgent.php`)**: Automatically categorizes, prioritizes, and identifies escalation risks on new tickets. It runs instantly on ticket creation.
2. **ReplyDraftAgent (`app/AI/Agents/ReplyDraftAgent.php`)**: Generates high-quality, professional drafts for support agents. It is grounded by articles retrieved via the `SearchKnowledgeBaseTool`.

All AI executions are recorded in the `ai_runs` database table for auditing, tracking the initiator (agent), input hash, status, JSON output, and any encountered errors. This ensures transparency and accountability for all automated triage and draft generation.

---

## �️‍♂️ Demo Steps (2 Minutes)

To evaluate the application's core capabilities, follow this flow:

### Step 1: Requester Experience
1. **Login** or Register as a requester (e.g., `requester@agentdesk.test` / `password`).
2. Navigate to **My Tickets** -> **Create Ticket**.
3. Fill out a detailed support request and attach a file.
4. Verify the ticket appears in your list.

### Step 2: Agent Operations & AI Triage
1. **Login** as a support agent (e.g., `agent@agentdesk.test` / `password`).
2. Navigate to the **Triage Queue**. Notice the ticket you just created is automatically prioritized and categorized by the AI.
3. Click **Assign to me**. You will be redirected to the **My Tickets** view where your assigned tickets are listed.
4. Click on the ticket details.
5. Watch the polling UI update from *Queued* to *Running* to *Succeeded*, then review the AI's generated draft based on the Knowledge Base.
6. Click **Use Draft**, edit the public reply if needed, attach a file, and click **Send Reply**.
7. Optionally, add a private internal note for staff visibility.
8. Change the ticket status to *In Progress*.

### Step 3: Requester Follow-Up
1. Switch back to the Requester account.
2. Open the ticket and observe that the Agent's public reply is visible, but the internal note is completely hidden.
3. Download the attachment provided by the agent.
4. Reply back to the ticket.

### Step 4: SLA & Notifications
1. Switch back to the Agent account. Check the **Notification Bell** to see the "Requester Replied" alert.
2. Resolve the ticket.
3. Observe that after resolution, the reply editor is hidden for both the Agent and the Requester, preventing any further updates to the resolved ticket.
4. In your terminal, manually trigger the SLA check: `php artisan schedule:run`.
4. Check the Notification Bell again to see alerts for any seeded tickets that missed their targets.

### Step 5: Admin Auditing
1. **Login** as an admin (`admin@agentdesk.test` / `password`).
2. Visit **Admin Dashboard** to see system stats.
3. Visit **Audit Log** to trace all status changes, assignments, and configurations.
4. Visit **AI Runs** to review the payloads and prompts from the background AI agents.
5. Visit **Export** to download a comprehensive CSV of all tickets.
