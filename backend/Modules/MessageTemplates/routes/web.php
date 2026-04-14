<?php

use Illuminate\Support\Facades\Route;
use Modules\MessageTemplates\Http\Controllers\MessageTemplatesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('messagetemplates', MessageTemplatesController::class)->names('messagetemplates');
});
