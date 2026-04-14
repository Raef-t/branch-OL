<?php

use Illuminate\Support\Facades\Route;
use Modules\Audits\Http\Controllers\AuditsController;

Route::group([
  //  'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
'middleware' => ['api'],
    'prefix' => 'audits',
    'as' => 'api.audits.',
], function () {
    Route::get('/', [AuditsController::class, 'index'])->name('index');
    Route::get('/latest', [AuditsController::class, 'latest'])->name('latest');
});
