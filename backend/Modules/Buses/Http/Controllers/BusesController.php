<?php

namespace Modules\Buses\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Buses\Models\Bus;
use Modules\Buses\Http\Requests\StoreBusRequest;
use Modules\Buses\Http\Requests\UpdateBusRequest;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Buses\Http\Resources\BusesResource;

class BusesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/buses",
     *     summary="قائمة جميع الباصات",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع الباصات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع الباصات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="School Bus"),
     *                     @OA\Property(property="capacity", type="integer", example=50),
     *                     @OA\Property(property="driver_name", type="string", example="John Doe"),
     *                     @OA\Property(property="route_description", type="string", example="Route from A to B"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد باصات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي باص مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // ترتيب الباصات من الأحدث إلى الأقدم
        $buses = Bus::orderBy('id', 'desc')->get();

        if ($buses->isEmpty()) {
            return $this->error('لا يوجد أي باص مسجل حالياً', 404);
        }

        return $this->successResponse(
            BusesResource::collection($buses),
            'تم جلب جميع الباصات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/buses/{id}",
     *     summary="عرض تفاصيل باص محدد",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الباص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات الباص بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات الباص بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="School Bus"),
     *                 @OA\Property(property="capacity", type="integer", example=50),
     *                 @OA\Property(property="driver_name", type="string", example="John Doe"),
     *                 @OA\Property(property="route_description", type="string", example="Route from A to B"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الباص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الباص غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $bus = Bus::find($id);

        if (!$bus) {
            return $this->error('الباص غير موجود', 404);
        }

        return $this->successResponse(
            new BusesResource($bus),
            'تم جلب بيانات الباص بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/buses",
     *     summary="إضافة باص جديد",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="New Bus"),
     *             @OA\Property(property="capacity", type="integer", example=50),
     *             @OA\Property(property="driver_name", type="string", example="John Doe"),
     *             @OA\Property(property="route_description", type="string", example="Route from A to B"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء الباص بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء الباص بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="New Bus"),
     *                 @OA\Property(property="capacity", type="integer", example=50),
     *                 @OA\Property(property="driver_name", type="string", example="John Doe"),
     *                 @OA\Property(property="route_description", type="string", example="Route from A to B"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreBusRequest $request)
    {
        $bus = Bus::create($request->validated());

        return $this->successResponse(
            new BusesResource($bus),
            'تم إنشاء الباص بنجاح',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/api/buses/{id}",
     *     summary="تحديث بيانات باص",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الباص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Bus"),
     *             @OA\Property(property="capacity", type="integer", example=60),
     *             @OA\Property(property="driver_name", type="string", example="Jane Doe"),
     *             @OA\Property(property="route_description", type="string", example="Updated route from C to D"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات الباص بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات الباص بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Bus"),
     *                 @OA\Property(property="capacity", type="integer", example=60),
     *                 @OA\Property(property="driver_name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="route_description", type="string", example="Updated route from C to D"),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-02T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الباص غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الباص غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateBusRequest $request, $id)
    {
        $bus = Bus::find($id);

        if (!$bus) {
            return $this->error('الباص غير موجود', 404);
        }

        $bus->update($request->validated());

        return $this->successResponse(
            new BusesResource($bus),
            'تم تحديث بيانات الباص بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/buses/{id}",
     *     summary="حذف باص (بعد التأكيد)",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الباص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="force",
     *         in="query",
     *         required=false,
     *         description="حذف قسري في حال وجود طلاب",
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Response(response=200, description="تم حذف الباص بنجاح"),
     *     @OA\Response(response=404, description="الباص غير موجود"),
     *     @OA\Response(response=409, description="يوجد طلاب مرتبطون بالباص")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $bus = Bus::find($id);

        if (!$bus) {
            return $this->error('الباص غير موجود', 404);
        }

        if ($bus->students()->exists() && !$request->boolean('force')) {
            return $this->error(
                'يجب تأكيد الحذف لوجود طلاب مرتبطين بالباص',
                409
            );
        }

        $bus->delete();

        return $this->successResponse(
            null,
            'تم حذف الباص بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/buses/{id}/delete-check",
     *     summary="فحص إمكانية حذف باص",
     *     tags={"Buses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف الباص",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="نتيجة فحص الحذف",
     *         @OA\JsonContent(
     *             @OA\Property(property="can_delete", type="boolean", example=false),
     *             @OA\Property(
     *                 property="relations",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"الطلاب"}
     *             ),
     *             @OA\Property(property="message", type="string", example="الباص مرتبط بطلاب")
     *         )
     *     ),
     *     @OA\Response(response=404, description="الباص غير موجود")
     * )
     */
    public function checkDelete($id)
    {
        $bus = Bus::find($id);

        if (!$bus) {
            return $this->error('الباص غير موجود', 404);
        }

        $relations = [];

        if ($bus->students()->exists()) {
            $relations[] = 'الطلاب';
        }

        if (!empty($relations)) {
            return $this->successResponse([
                'can_delete' => false,
                'relations'  => $relations,
                'message'    => 'لا يمكن حذف الباص لوجود ارتباطات'
            ], 'تحذير قبل الحذف');
        }

        return $this->successResponse([
            'can_delete' => true,
            'relations'  => [],
            'message'    => 'يمكن حذف الباص بأمان'
        ], 'لا توجد ارتباطات');
    }
}
