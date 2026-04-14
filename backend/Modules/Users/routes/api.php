<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\AuthController;
use Modules\Users\Http\Controllers\UserApprovalController;
use Modules\Users\Http\Controllers\UsersController;
// -------------------------------
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Google\Client as GoogleClient;
/*
|--------------------------------------------------------------------------
| Routes عامة (لا تتطلب توكن)
|--------------------------------------------------------------------------
*/

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');



Route::get('/test-fcm', function (Request $request) {

    $token = $request->query('token');

    if (!$token) {
        return response()->json([
            'success' => false,
            'message' => 'FCM token is required'
        ], 422);
    }

    $client = new GoogleClient();
    $client->setAuthConfig(storage_path('app/firebase-service-account.json'));
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

    $url = 'https://fcm.googleapis.com/v1/projects/my-laravel-notifications/messages:send';

    $payload = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => 'اختبار فايربايس',
                'body'  => 'إذا وصلتك هذه الرسالة فالإعداد صحيح'
            ],
            'data' => [
                'type' => 'test'
            ]
        ]
    ];

    /** @var Response $response */
    $response = Http::withToken($accessToken)->post($url, $payload);

    return $response->json();
});

/*
|--------------------------------------------------------------------------
| Routes محمية — تتطلب توكن + حساب مُفعّل + كلمة مرور غير مؤقتة
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix' => 'users',
    'as' => 'api.users.',
], function () {
    Route::get('/', [UsersController::class, 'index'])->name('index');
    Route::post('/', [UsersController::class, 'store'])->name('store');
    Route::get('/{id}', [UsersController::class, 'show'])->name('show');
    Route::put('/{id}', [UsersController::class, 'update'])->name('update');
    Route::delete('/{id}', [UsersController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/roles', [UsersController::class, 'assignRole'])->name('assignRole');
    Route::delete('/{id}/roles/{role}', [UsersController::class, 'removeRole'])->name('removeRole');
    Route::post('/{id}/toggle-status', [UsersController::class, 'toggleStatus'])->name('toggleStatus');
    Route::post('/{id}/reset-password', [UsersController::class, 'resetPassword'])->name('resetPassword');
});

/*
|--------------------------------------------------------------------------
| Routes محمية جزئيًا — تتطلب توكن فقط (لا تتطلب force-password-change)
|--------------------------------------------------------------------------
*/

// logout: لا يحتاج إلى force-password-change (حتى لو كانت كلمة المرور مؤقتة)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['api', 'auth:sanctum'])
    ->name('api.auth.logout');

// change-password: الاستثناء الوحيد المسموح به عند force_password_change = true
Route::post('/users/change-password', [UsersController::class, 'changePassword'])
    ->middleware(['api', 'auth:sanctum'])
    ->name('api.users.change-password');

// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
// Route::post('users/{id}/approve', [UserApprovalController::class, 'approve'])
//     ->name('api.users.approve');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('users/{id}/approve', [UserApprovalController::class, 'approve'])
        ->name('api.users.approve');
});
