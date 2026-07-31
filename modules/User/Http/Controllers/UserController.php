<?php

declare(strict_types = 1);

namespace App\User\Http\Controllers;

use App\User\Http\Requests\UpdateUserRequest;
use App\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Attributes\Controllers\Authorize;

/**
 * Handle user profile operations.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
final class UserController
{
    /**
     * Display the specified user.
     *
     * @param  \App\User\Models\User  $user
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    #[Authorize('view', 'user')]
    public function show(User $user): JsonResource
    {
        return $user->toResource();
    }

    /**
     * Update the specified user.
     *
     * @param  \App\User\Http\Requests\UpdateUserRequest  $request
     * @param  \App\User\Models\User  $user
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    #[Authorize('update', 'user')]
    public function update(UpdateUserRequest $request, User $user): JsonResource
    {
        $user->update($request->validated());

        return $user->toResource();
    }

    /**
     * Remove the specified user.
     *
     * @param  \App\User\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    #[Authorize('delete', 'user')]
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
