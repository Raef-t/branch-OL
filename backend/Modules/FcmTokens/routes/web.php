<?php

use Illuminate\Support\Facades\Route;
use Modules\FcmTokens\Http\Controllers\FcmTokenController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('fcmtokens', FcmTokenController::class)->names('fcmtokens');
});
