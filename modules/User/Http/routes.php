<?php

use App\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|-------------------------------------------------------------------------------
| User Routes
|-------------------------------------------------------------------------------
|
| Routes for user management. These are loaded automatically by the modular
| architecture and prefixed as API routes.
|
*/

Route::middleware('auth')
    ->apiResource('users', UserController::class)
    ->only(['show', 'update', 'destroy']);
