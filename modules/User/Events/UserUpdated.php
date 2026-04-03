<?php

namespace App\User\Events;

use App\User\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when a user profile is updated.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UserUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\User\Models\User  $user
     * @return void
     */
    public function __construct(

        /** The user that was updated. */
        public readonly User $user,

    ) {}
}
