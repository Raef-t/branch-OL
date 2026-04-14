<?php

use Illuminate\Support\Facades\Route;
use Modules\NotificationRecipients\Http\Controllers\NotificationRecipientsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('notificationrecipients', NotificationRecipientsController::class)->names('notificationrecipients');
});
