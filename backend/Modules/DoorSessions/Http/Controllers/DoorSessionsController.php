<?php

namespace Modules\DoorSessions\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\DoorSessions\models\DoorSession;
use Modules\DoorSessions\Http\Requests\StoreDoorSessionRequest;
use Modules\DoorSessions\Http\Requests\UpdateDoorSessionRequest;
use Modules\DoorSessions\Http\Resources\DoorSessionResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class DoorSessionsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/door-sessions",
     *     summary="قائمة جميع جلسات الأبواب",
     *     tags={"DoorSessions"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع جلسات الأبواب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع جلسات الأبواب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="device_id", type="integer", example=1),
     *                     @OA\Property(property="session_token", type="string", example="abc123xyz"),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T11:52:00Z"),
     *                     @OA\Property(property="is_used", type="boolean", example=false),
     *                     @OA\Property(property="student_id", type="integer", example=1, nullable=true),
     *                     @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T11:52:30Z", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:52:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد جلسات أبواب",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي جلسة أبواب مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $doorSessions = DoorSession::all();

        if ($doorSessions->isEmpty()) {
            return $this->error('لا يوجد أي جلسة أبواب مسجلة حالياً', 404);
        }

        return $this->successResponse(
            DoorSessionResource::collection($doorSessions),
            'تم جلب جميع جلسات الأبواب بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/door-sessions",
     *     summary="إضافة جلسة باب جديدة",
     *     tags={"DoorSessions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id","session_token","expires_at"},
     *             @OA\Property(property="device_id", type="integer", example=1),
     *             @OA\Property(property="session_token", type="string", example="abc123xyz"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T12:52:00Z"),
     *             @OA\Property(property="is_used", type="boolean", example=false),
     *             @OA\Property(property="student_id", type="integer", example=1, nullable=true),
     *             @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T11:52:30Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء جلسة الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء جلسة الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="integer", example=1),
     *                 @OA\Property(property="session_token", type="string", example="abc123xyz"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T12:52:00Z"),
     *                 @OA\Property(property="is_used", type="boolean", example=false),
     *                 @OA\Property(property="student_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T11:52:30Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:52:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreDoorSessionRequest $request)
    {
        $doorSession = DoorSession::create($request->validated());

        return $this->successResponse(
            new DoorSessionResource($doorSession),
            'تم إنشاء جلسة الباب بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/door-sessions/{id}",
     *     summary="عرض تفاصيل جلسة باب محددة",
     *     tags={"DoorSessions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جلسة الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات جلسة الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات جلسة الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="integer", example=1),
     *                 @OA\Property(property="session_token", type="string", example="abc123xyz"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T12:52:00Z"),
     *                 @OA\Property(property="is_used", type="boolean", example=false),
     *                 @OA\Property(property="student_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T11:52:30Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:52:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جلسة الباب غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جلسة الباب غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $doorSession = DoorSession::find($id);

        if (!$doorSession) {
            return $this->error('جلسة الباب غير موجودة', 404);
        }

        return $this->successResponse(
            new DoorSessionResource($doorSession),
            'تم جلب بيانات جلسة الباب بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/door-sessions/{id}",
     *     summary="تحديث بيانات جلسة باب",
     *     tags={"DoorSessions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جلسة الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="device_id", type="integer", example=2),
     *             @OA\Property(property="session_token", type="string", example="xyz456abc"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T13:52:00Z"),
     *             @OA\Property(property="is_used", type="boolean", example=true),
     *             @OA\Property(property="student_id", type="integer", example=2, nullable=true),
     *             @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T12:52:30Z", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات جلسة الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات جلسة الباب بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="device_id", type="integer", example=2),
     *                 @OA\Property(property="session_token", type="string", example="xyz456abc"),
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2025-09-29T13:52:00Z"),
     *                 @OA\Property(property="is_used", type="boolean", example=true),
     *                 @OA\Property(property="student_id", type="integer", example=2, nullable=true),
     *                 @OA\Property(property="used_at", type="string", format="date-time", example="2025-09-29T12:52:30Z", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T11:52:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جلسة الباب غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جلسة الباب غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateDoorSessionRequest $request, $id)
    {
        $doorSession = DoorSession::find($id);

        if (!$doorSession) {
            return $this->error('جلسة الباب غير موجودة', 404);
        }

        $doorSession->update($request->validated());

        return $this->successResponse(
            new DoorSessionResource($doorSession),
            'تم تحديث بيانات جلسة الباب بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/door-sessions/{id}",
     *     summary="حذف جلسة باب",
     *     tags={"DoorSessions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف جلسة الباب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف جلسة الباب بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف جلسة الباب بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="جلسة الباب غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="جلسة الباب غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $doorSession = DoorSession::find($id);

        if (!$doorSession) {
            return $this->error('جلسة الباب غير موجودة', 404);
        }

        $doorSession->delete();

        return $this->successResponse(
            null,
            'تم حذف جلسة الباب بنجاح',
            200
        );
    }
}