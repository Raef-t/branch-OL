<?php

namespace Modules\Schools\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Schools\Http\Requests\StoreSchoolRequest;
use Modules\Schools\Http\Requests\UpdateSchoolRequest;
use Modules\Schools\Models\School;
use Modules\Schools\Http\Resources\SchoolResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class SchoolsController extends Controller
{
    use SuccessResponseTrait;
    /**
     * Display a listing of the resource.
     */
  /**
 * @OA\Get(
 *     path="/api/schools",
 *     summary="قائمة جميع المدارس",
 *     tags={"Schools"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب جميع المدارس بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب جميع المدارس بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
 *                     @OA\Property(property="type", type="string", example="public"),
 *                     @OA\Property(property="city", type="string", example="دمشق"),
 *                     @OA\Property(property="notes", type="string", example="مدرسة حكومية"),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(property="created_at", type="string", example="2025-01-01 10:00:00")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="لا يوجد مدارس",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="لا يوجد أي مدرسة مسجلة حالياً"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
public function index()
{
    $schools = School::orderBy('name')->get();

    if ($schools->isEmpty()) {
        return $this->error(
            'لا يوجد أي مدرسة مسجلة حالياً',
            404
        );
    }

   return $this->SuccessResponse(
    SchoolResource::collection($schools),
    'تم جلب جميع المدارس بنجاح',
    200
);

}


        /**
         * Show the form for creating a new resource.
         */
        public function create()
        {
            return view('schools::create');
        }
    /**
     * @OA\Post(
     *     path="/api/schools",
     *     summary="إضافة مدرسة جديدة",
     *     tags={"Schools"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
     *             @OA\Property(property="type", type="string", example="public", enum={"public","private","other"}),
     *             @OA\Property(property="city", type="string", example="دمشق"),
     *             @OA\Property(property="notes", type="string", example="مدرسة حكومية"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء المدرسة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء المدرسة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
     *                 @OA\Property(property="type", type="string", example="public"),
     *                 @OA\Property(property="city", type="string", example="دمشق"),
     *                 @OA\Property(property="notes", type="string", example="مدرسة حكومية"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", example="2025-01-01 10:00:00")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="بيانات غير صالحة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="فشل التحقق من البيانات"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255|unique:schools,name',
            'type'      => 'nullable|in:public,private,other',
            'city'      => 'nullable|string|max:255',
            'notes'     => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $school = School::create($validated);

        return $this->successResponse(
            new SchoolResource($school),
            'تم إنشاء المدرسة بنجاح',
            201
        );
    }


    /**
     * Show the specified resource.
     */
   /**
 * @OA\Get(
 *     path="/api/schools/{id}",
 *     summary="عرض بيانات مدرسة واحدة",
 *     tags={"Schools"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدرسة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب بيانات المدرسة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب بيانات المدرسة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
 *                 @OA\Property(property="type", type="string", example="public"),
 *                 @OA\Property(property="city", type="string", example="دمشق"),
 *                 @OA\Property(property="notes", type="string", example="مدرسة حكومية"),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", example="2025-01-01 10:00:00")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="المدرسة غير موجودة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="المدرسة غير موجودة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
public function show($id)
{
    $school = School::find($id);
    return $this->successResponse(
        new SchoolResource($school),
        'تم جلب بيانات المدرسة بنجاح',
        200
    );
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('schools::edit');
    }

    /**
     * @OA\Put(
     *     path="/api/schools/{id}",
     *     summary="تحديث بيانات مدرسة",
     *     tags={"Schools"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدرسة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
     *             @OA\Property(property="type", type="string", example="public", enum={"public","private","other"}),
     *             @OA\Property(property="city", type="string", example="دمشق"),
     *             @OA\Property(property="notes", type="string", example="تم تحديث البيانات"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات المدرسة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المدرسة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="ثانوية ابن رشد"),
     *                 @OA\Property(property="type", type="string", example="public"),
     *                 @OA\Property(property="city", type="string", example="دمشق"),
     *                 @OA\Property(property="notes", type="string", example="تم تحديث البيانات"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="updated_at", type="string", example="2025-01-01 12:00:00")
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateSchoolRequest $request, $id)
    {
        $school = School::find($id);
        if (!$school) {
            return $this->error('المدرسة غير موجودة', 404);
        }
        $school->update($request->validated());

        return $this->successResponse(
            new SchoolResource($school),
            'تم تحديث بيانات المدرسة بنجاح',
            200
        );
    }

/**
 * @OA\Delete(
 *     path="/api/schools/{id}",
 *     summary="حذف مدرسة",
 *     tags={"Schools"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدرسة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم حذف المدرسة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم حذف المدرسة بنجاح"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=409,
 *         description="لا يمكن حذف المدرسة لوجود طلاب مرتبطين بها",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="لا يمكن حذف المدرسة لوجود طلاب مرتبطين بها"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
public function destroy(Request $request, $id)
{
    $school = School::find($id);
    // حماية اختيارية: منع الحذف إذا كان هناك طلاب
    if (!$school) {
        return $this->error('المدرسة غير موجودة', 404);
    }

    $school->delete();

    return $this->successResponse(
        null,
        'تم حذف المدرسة بنجاح',
        200
    );
}
   
}
