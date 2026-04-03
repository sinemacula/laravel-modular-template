<?php

namespace App\User\Observers;

use App\User\Events\UserUpdated;
use App\User\Models\User;

/**
 * Observe User model lifecycle events.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param  \App\User\Models\User  $user
     * @return void
     */
    public function updated(User $user): void
    {
        UserUpdated::dispatch($user);
    }
}
