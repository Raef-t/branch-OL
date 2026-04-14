<?php

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Http\Controllers\PermissionsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('permissions', PermissionsController::class)->names('permissions');
});
