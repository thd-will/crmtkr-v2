<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        if ($event->user) {
            activity()
                ->causedBy($event->user)
                ->withProperties([
                    'ip' => request()->ip(),
                    'logout_time' => now(),
                ])
                ->log("ออกจากระบบ: {$event->user->name}");
        }
    }
}
