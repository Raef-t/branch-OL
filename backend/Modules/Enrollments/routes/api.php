<?php
use Illuminate\Support\Facades\Route;
use Modules\Enrollments\Http\Controllers\EnrollmentsController;
use Modules\Enrollments\Http\Controllers\QrCodeController;

Route::group([
    'middleware' => ['api', 'auth:sanctum'],
    'prefix' => 'enrollments',
    'as' => 'api.enrollments.',
], function () {

    Route::get('/', [EnrollmentsController::class, 'index'])
        ->middleware('permission:enrollments.view')
        ->name('index');

    Route::post('/', [EnrollmentsController::class, 'store'])
       // ->middleware('permission:enrollments.create')
        ->name('store');

    Route::post('/scan-qr', [QrCodeController::class, 'scanQr'])
       // ->middleware('permission:enrollments.view')
        ->name('scan-qr');

    Route::get('/qr-code', [QrCodeController::class, 'generate'])
        //->middleware('permission:enrollments.view')
        ->name('qr-code');
});
