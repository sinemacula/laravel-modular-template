<?php

declare(strict_types = 1);

namespace App\User\Observers;

use App\User\Events\UserUpdated;
use App\User\Models\User;

/**
 * Observe User model lifecycle events.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class UserObserver
{
    /**
     * Handle the User "updated" event.
     *
     * @param  \App\User\Models\User  $user
     * @return void
     */
    public function updated(User $user): void
    {
        event(new UserUpdated($user));
    }
}
