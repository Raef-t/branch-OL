<?php

use Illuminate\Support\Facades\Route;
use Modules\Permissions\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| Routes محمية — تتطلب توكن + حساب مُفعّل + كلمة مرور غير مؤقتة
|--------------------------------------------------------------------------
*/

Route::get('/test-roles', function () {
    $service = app(\Modules\Permissions\Services\RolePermissionService::class);
    try {
        $roles = $service->getAllRoles();
        return $roles;
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'error'  => $e->getMessage(),
        ], 500);
    }
});

Route::group([
    'middleware' => ['api', 'auth:sanctum', 'approved', 'force-password-change'],
    'prefix'     => 'roles',
    'as'         => 'api.roles.',
], function () {

    // جلب قائمة الصلاحيات (قراءة فقط)
    Route::get('permissions/list', [RoleController::class, 'listPermissions'])
        //->middleware('permission:permissions.view')
        ->name('permissions.list');

    // عرض الأدوار وقائمة التفاصيل (قراءتان)
    Route::get('/', [RoleController::class, 'index'])
        // ->middleware('permission:roles.view')
        ->name('roles.index');
    Route::get('/{role}', [RoleController::class, 'show'])
       // ->middleware('permission:roles.view')
        ->name('roles.show');

    // كافة عمليات التعديل على الأدوار (إنشاء، تعديل، حذف، ربط) تحتاج إلى roles.manage
    Route::post('/', [RoleController::class, 'store'])
        // ->middleware('permission:roles.manage')
        ->name('roles.store');

    Route::put('/{role}', [RoleController::class, 'update'])
        // ->middleware('permission:roles.manage')
        ->name('roles.update');

    Route::delete('/{role}', [RoleController::class, 'destroy'])
        // ->middleware('permission:roles.manage')
        ->whereNumber('role') 
        ->name('roles.destroy');

    Route::post('/assign', [RoleController::class, 'assignRole'])
        // ->middleware('permission:roles.manage')
        ->name('roles.assign');

    Route::post('/assign-multiple', [RoleController::class, 'assignMultiple'])
        // ->middleware('permission:roles.manage')
        ->name('roles.assign-multiple');

    Route::delete('/remove', [RoleController::class, 'removeRole'])
        // ->middleware('permission:roles.manage')
        ->name('roles.remove');

    // إزالة عدة أدوار
    Route::delete('/remove-multiple', [RoleController::class, 'removeMultiple'])
        // ->middleware('permission:roles.manage')
        ->name('roles.remove-multiple');
});
