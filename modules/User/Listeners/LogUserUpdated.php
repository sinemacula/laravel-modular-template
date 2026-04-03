<?php

namespace App\User\Listeners;

use App\User\Events\UserUpdated;
use Illuminate\Support\Facades\Log;

/**
 * Log when a user profile is updated.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class LogUserUpdated
{
    /**
     * Handle the event.
     *
     * @param  \App\User\Events\UserUpdated  $event
     * @return void
     */
    public function handle(UserUpdated $event): void
    {
        Log::info('User updated', [
            'user_id' => $event->user->getKey(),
        ]);
    }
}
