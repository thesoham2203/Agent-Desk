<?php

declare(strict_types=1);

use App\Jobs\CheckOverdueTargetsJob;
use Illuminate\Support\Facades\Schedule;

/**
 * Schedule the overdue check to run every hour.
 *
 * HOW THE SCHEDULER WORKS:
 * You set up ONE system cron: * * * * * php artisan schedule:run
 * That cron fires every minute.
 * Laravel checks internally which jobs are due to run.
 * ->everyHour() means Laravel only dispatches the job
 * once per hour, even though schedule:run fires every minute.
 *
 * FOR DEMO: use ->everyMinute() temporarily so you can
 * demonstrate it working without waiting an hour.
 * Change back to ->hourly() before final submission.
 */
Schedule::job(new CheckOverdueTargetsJob())->everyMinute();

// To test manually without waiting:
// php artisan schedule:run
// Or dispatch directly:
// php artisan tinker → App\Jobs\CheckOverdueTargetsJob::dispatchSync()
