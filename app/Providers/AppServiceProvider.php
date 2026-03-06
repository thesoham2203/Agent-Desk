<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\AiRun;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\KbArticle;
use App\Models\Macro;
use App\Models\SlaConfig;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Policies\AiRunPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\KbArticlePolicy;
use App\Policies\MacroPolicy;
use App\Policies\SlaConfigPolicy;
use App\Policies\TicketMessagePolicy;
use App\Policies\TicketPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * ============================================================
 * FILE: AppServiceProvider.php
 * LAYER: ServiceProvider
 * ============================================================
 *
 * WHAT IS THIS?
 * The primary service provider for booting the application's services.
 *
 * WHY DOES IT EXIST?
 * To register the application's authorization policies centrally.
 *
 * HOW IT FITS IN THE APP:
 * Laravel calls register() then boot() when the framework starts up.
 *
 * LARAVEL CONCEPT EXPLAINED:
 * The AppServiceProvider is the central place for all Laravel
 * bootstrapping. Policies registered here tell the Gate facade which
 * policy class to consult when checking permissions on a specific model.
 * ============================================================
 */
final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Primarily guards full CRUD actions and role-based ticket views (Agent/Admin vs Requester)
        Gate::policy(Ticket::class, TicketPolicy::class);

        // Primarily guards internal note visibility (Agent/Admin only)
        Gate::policy(TicketMessage::class, TicketMessagePolicy::class);

        // Primarily guards attachment downloads based on ticket viewing rights
        Gate::policy(Attachment::class, AttachmentPolicy::class);

        // Primarily guards category management (Admin only)
        Gate::policy(Category::class, CategoryPolicy::class);

        // Primarily guards macro management (Admin only) and usage (Agent)
        Gate::policy(Macro::class, MacroPolicy::class);

        // Primarily guards SLA configuration (Admin only)
        Gate::policy(SlaConfig::class, SlaConfigPolicy::class);

        // Primarily guards KB article management (Admin) and reading (Agent)
        Gate::policy(KbArticle::class, KbArticlePolicy::class);

        // Primarily guards triggering AI execution and viewing output traces (Agent/Admin)
        Gate::policy(AiRun::class, AiRunPolicy::class);

        // Primarily guards sensitive system-wide history review (Admin only)
        Gate::policy(AuditLog::class, AuditLogPolicy::class);

        /**
         * Administrative Gate Abilities
         */
        Gate::define('manage-categories', fn (User $user): bool => $user->role === UserRole::Admin);
        Gate::define('manage-macros', fn (User $user): bool => $user->role === UserRole::Admin);
        Gate::define('manage-sla-config', fn (User $user): bool => $user->role === UserRole::Admin);
        Gate::define('manage-kb-articles', fn (User $user): bool => $user->role === UserRole::Admin);
        Gate::define('view-audit-log', fn (User $user): bool => $user->role === UserRole::Admin);
        Gate::define('view-ai-runs', fn (User $user): bool => $user->role === UserRole::Admin);
    }
}

/**
 * ============================================================
 * WHAT TO READ NEXT:
 * ============================================================
 * Now that you understand AppServiceProvider.php, the next logical file
 * to read is:
 *
 * → tests/Feature/Policies/TicketPolicyTest.php
 *
 * WHY: It's time to see how the system verifies these policies
 * practically by reading through the automated feature tests.
 * ============================================================
 */
