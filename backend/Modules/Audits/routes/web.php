<?php

use Illuminate\Support\Facades\Route;
use Modules\Audits\Http\Controllers\AuditsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('audits', AuditsController::class)->names('audits');
});
