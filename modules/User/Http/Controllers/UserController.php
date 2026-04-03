<?php

namespace App\User\Http\Controllers;

use App\User\Http\Requests\UpdateUserRequest;
use App\User\Http\Resources\UserResource;
use App\User\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Handle user profile operations.
 *
 * @author      Ben Carey <bdmc@sinemacula.co.uk>
 * @copyright   2026 Sine Macula Limited
 */
class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Automatically authorize resource actions against UserPolicy.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\User\Models\User  $user
     * @return \App\User\Http\Resources\UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified user.
     *
     * @param  \App\User\Http\Requests\UpdateUserRequest  $request
     * @param  \App\User\Models\User  $user
     * @return \App\User\Http\Resources\UserResource
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Remove the specified user.
     *
     * @param  \App\User\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
