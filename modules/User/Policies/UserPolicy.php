<?php

namespace App\User\Policies;

use App\User\Models\User;

/**
 * Authorization policy for user operations.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User\Models\User  $auth
     * @param  \App\User\Models\User  $user
     * @return bool
     */
    public function view(User $auth, User $user): bool
    {
        return $auth->is($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User\Models\User  $auth
     * @param  \App\User\Models\User  $user
     * @return bool
     */
    public function update(User $auth, User $user): bool
    {
        return $auth->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User\Models\User  $auth
     * @param  \App\User\Models\User  $user
     * @return bool
     */
    public function delete(User $auth, User $user): bool
    {
        return $auth->is($user);
    }
}
