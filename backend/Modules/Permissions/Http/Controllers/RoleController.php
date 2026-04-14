<?php

namespace Modules\Permissions\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Permissions\Http\Requests\AssignRoleRequest;
use Modules\Permissions\Http\Requests\BulkAssignRolesRequest;
use Modules\Permissions\Http\Requests\BulkRemoveRolesRequest;
use Modules\Permissions\Http\Requests\RemoveRoleRequest;
use Modules\Permissions\Http\Requests\StoreRoleRequest;
use Modules\Permissions\Services\RolePermissionService;
use Modules\Permissions\Http\Resources\RoleResource;
use Modules\Permissions\Http\Resources\PermissionResource;
use Modules\Permissions\Http\Resources\UserRolesResource;
use Spatie\Permission\Models\Role;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="إدارة الأدوار والصلاحيات"
 * )
 */
class RoleController extends Controller
{
    protected $service;

    public function __construct(RolePermissionService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="عرض جميع الأدوار مع صلاحياتهم",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الأدوار بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="خطأ داخلي في الخادم",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function index()
    {
        try {
            $roles = $this->service->getAllRoles();
            return RoleResource::collection($roles);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء جلب الأدوار',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="إنشاء دور جديد",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الدور بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function store(StoreRoleRequest $request)
    {
        $role = $this->service->createRole($request->validated());
        return new RoleResource($role);
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="عرض تفاصيل دور محدد",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرف الدور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الدور بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الدور غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الدور غير موجود")
     *         )
     *     )
     * )
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return new RoleResource($role);
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="تحديث دور",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرف الدور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث الدور بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/RoleResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="فشل التحقق من البيانات",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function update(StoreRoleRequest $request, Role $role)
    {
        $role = $this->service->updateRole($role, $request->validated());
        return new RoleResource($role);
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="حذف دور",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="معرف الدور",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="تم حذف الدور بنجاح"
     *     )
     * )
     */
    public function destroy(Role $role)
    {
        $this->service->deleteRole($role);
        return response()->noContent();
    }

    /**
     * @OA\Get(
     *     path="/api/roles/permissions/list",
     *     summary="جلب قائمة جميع الصلاحيات",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الصلاحيات بنجاح",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PermissionResource")
     *         )
     *     )
     * )
     */
    public function listPermissions()
    {
        $permissions = $this->service->getAllPermissions();
        return PermissionResource::collection($permissions);
    }

    /**
     * @OA\Post(
     *     path="/api/roles/assign",
     *     summary="ربط دور بمستخدم",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AssignRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم ربط الدور بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/UserRolesResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="فشل في ربط الدور",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function assignRole(AssignRoleRequest $request)
    {
        try {
            $user = $this->service->assignRoleToUser(
                $request->user_id,
                $request->role_name
            );
            return new UserRolesResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء ربط الدور بالمستخدم',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/roles/assign-multiple",
     *     summary="ربط عدة أدوار بمستخدم دفعة واحدة",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=123),
     *             @OA\Property(
     *                 property="role_names",
     *                 type="array",
     *                 @OA\Items(type="string", example="editor")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم ربط الأدوار بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/UserRolesResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="فشل العملية",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function assignMultiple(BulkAssignRolesRequest $request)
    {
        try {
            $user = $this->service->assignMultipleRolesToUser(
                $request->user_id,
                $request->role_names
            );

            return new UserRolesResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء ربط الأدوار بالمستخدم',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/remove",
     *     summary="إزالة دور من مستخدم",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RemoveRoleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إزالة الدور بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/UserRolesResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="فشل العملية",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function removeRole(RemoveRoleRequest $request)
    {
        try {
            $user = $this->service->removeRoleFromUser(
                $request->user_id,
                $request->role_name
            );
            return new UserRolesResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء إزالة الدور من المستخدم',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/remove-multiple",
     *     summary="إزالة عدة أدوار من مستخدم دفعة واحدة",
     *     tags={"Roles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BulkRemoveRolesRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم إزالة الأدوار بنجاح",
     *         @OA\JsonContent(ref="#/components/schemas/UserRolesResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="فشل العملية",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
     *     )
     * )
     */
    public function removeMultiple(BulkRemoveRolesRequest $request)
    {
        try {
            $user = $this->service->removeMultipleRolesFromUser(
                $request->user_id,
                $request->role_names
            );
            return new UserRolesResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'حدث خطأ أثناء إزالة الأدوار من المستخدم',
                'error'   => $e->getMessage(),
            ], 400);
        }
    }
}
