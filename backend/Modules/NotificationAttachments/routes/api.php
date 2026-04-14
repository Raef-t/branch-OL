<?php

use Illuminate\Support\Facades\Route;
use Modules\NotificationAttachments\Http\Controllers\NotificationAttachmentsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('notificationattachments', NotificationAttachmentsController::class)->names('notificationattachments');
});
