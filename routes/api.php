<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Role;
use App\Http\Controllers\RoleController;

Route::get('/api/roles', function () {
    return Role::select('id', 'name')->get();
});

// Role management API routes
Route::apiResource('roles', RoleController::class);
