<?php

use Illuminate\Support\Facades\Route;
use Modules\NotificationRecipients\Http\Controllers\NotificationRecipientsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('notificationrecipients', NotificationRecipientsController::class)->names('notificationrecipients');
});
