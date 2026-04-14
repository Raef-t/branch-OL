<?php

namespace Modules\ClassRooms\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Batches\Models\Batch;
use Modules\ClassRooms\Models\ClassRoom;
use Modules\BatchStudents\Models\BatchStudent;
use Modules\ClassRooms\Http\Requests\StoreClassRoomRequest;
use Modules\ClassRooms\Http\Requests\UpdateClassRoomRequest;
use Modules\ClassRooms\Http\Resources\ClassRoomResource;
use Modules\Shared\Traits\SuccessResponseTrait;

/**
 * @OA\Tag(
 *     name="Class Rooms",
 *     description="إدارة القاعات الدراسية"
 * )
 */
class ClassRoomsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/class-rooms",
     *     summary="قائمة جميع القاعات الدراسية",
     *     tags={"Class Rooms"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع القاعات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع القاعات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="قاعة 101"),
     *                     @OA\Property(property="code", type="string", example="C101"),
     *                     @OA\Property(property="capacity", type="integer", example=30),
     *                     @OA\Property(property="notes", type="string", example="مجهزة بمكيف"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد قاعات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي قاعات حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $rooms = ClassRoom::all();

        if ($rooms->isEmpty()) {
            return $this->error('لا يوجد أي قاعات حالياً', 404);
        }

        return $this->successResponse(
            ClassRoomResource::collection($rooms),
            'تم جلب جميع القاعات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/class-rooms",
     *     summary="إضافة قاعة دراسية جديدة",
     *     tags={"Class Rooms"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","code","capacity"},
     *             @OA\Property(property="name", type="string", example="قاعة 101"),
     *             @OA\Property(property="code", type="string", example="C101"),
     *             @OA\Property(property="capacity", type="integer", example=30),
     *             @OA\Property(property="notes", type="string", example="مجهزة بمكيف")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء القاعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء القاعة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="قاعة 101"),
     *                 @OA\Property(property="code", type="string", example="C101"),
     *                 @OA\Property(property="capacity", type="integer", example=30),
     *                 @OA\Property(property="notes", type="string", example="مجهزة بمكيف"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreClassRoomRequest $request)
    {
        $room = ClassRoom::create($request->validated());

        return $this->successResponse(
            new ClassRoomResource($room),
            'تم إنشاء القاعة بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/class-rooms/{id}",
     *     summary="عرض تفاصيل قاعة دراسية محددة",
     *     tags={"Class Rooms"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف القاعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات القاعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات القاعة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="قاعة 101"),
     *                 @OA\Property(property="code", type="string", example="C101"),
     *                 @OA\Property(property="capacity", type="integer", example=30),
     *                 @OA\Property(property="notes", type="string", example="مجهزة بمكيف"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="القاعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="القاعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $room = ClassRoom::find($id);

        if (!$room) {
            return $this->error('القاعة غير موجودة', 404);
        }

        return $this->successResponse(
            new ClassRoomResource($room),
            'تم جلب بيانات القاعة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/class-rooms/{id}",
     *     summary="تحديث بيانات قاعة دراسية",
     *     tags={"Class Rooms"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف القاعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="قاعة رقم 2"),
     *             @OA\Property(property="code", type="string", example="CR2"),
     *             @OA\Property(property="capacity", type="integer", example=40),
     *             @OA\Property(property="notes", type="string", example="تم إضافة سبورة ذكية")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات القاعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات القاعة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="قاعة رقم 2"),
     *                 @OA\Property(property="code", type="string", example="CR2"),
     *                 @OA\Property(property="capacity", type="integer", example=40),
     *                 @OA\Property(property="notes", type="string", example="تم إضافة سبورة ذكية"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-29T12:15:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-29T12:15:30Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="القاعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="القاعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateClassRoomRequest $request, $id)
    {
        $room = ClassRoom::find($id);

        if (!$room) {
            return $this->error('القاعة غير موجودة', 404);
        }

        $room->update($request->validated());

        return $this->successResponse(
            new ClassRoomResource($room),
            'تم تحديث بيانات القاعة بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/class-rooms/{id}",
     *     summary="حذف قاعة دراسية",
     *     tags={"Class Rooms"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف القاعة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف القاعة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف القاعة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="القاعة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="القاعة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $room = ClassRoom::find($id);

        if (!$room) {
            return $this->error('القاعة غير موجودة', 404);
        }

        $room->delete();

        return $this->successResponse(
            null,
            'تم حذف القاعة بنجاح',
            200
        );
    } 

}