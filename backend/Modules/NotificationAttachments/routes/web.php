<?php

use Illuminate\Support\Facades\Route;
use Modules\NotificationAttachments\Http\Controllers\NotificationAttachmentsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('notificationattachments', NotificationAttachmentsController::class)->names('notificationattachments');
});
