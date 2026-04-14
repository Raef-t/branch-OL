<?php

namespace Modules\Cities\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Modules\Cities\Models\City;
use Modules\Cities\Http\Requests\StoreCityRequest;
use Modules\Cities\Http\Requests\UpdateCityRequest;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Cities\Http\Resources\CityResource;

class CitiesController extends Controller
{
    use SuccessResponseTrait;


    /**
     * @OA\Get(
     *     path="/api/cities",
     *     summary="قائمة جميع المدن",
     *     tags={"Cities"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع المدن بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع المدن بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="دمشق"),
     *                     @OA\Property(property="description", type="string", example="العاصمة السورية"),
     *                     @OA\Property(property="is_active", type="boolean", example=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد مدن",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي مدينة مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // ترتيب المدن من الأحدث إلى الأقدم
        $cities = City::orderBy('id', 'desc')->get();

        if ($cities->isEmpty()) {
            return $this->error('لا يوجد أي مدينة مسجلة حالياً', 404);
        }

        return $this->successResponse(
            CityResource::collection($cities),
            'تم جلب جميع المدن بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/cities/{id}",
     *     summary="عرض تفاصيل مدينة محددة",
     *     tags={"Cities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدينة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات المدينة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات المدينة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="دمشق"),
     *                 @OA\Property(property="description", type="string", example="العاصمة السورية"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المدينة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدينة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $city = City::find($id);

        if (!$city) {
            return $this->error('المدينة غير موجودة', 404);
        }

        return $this->successResponse(
            new CityResource($city),
            'تم جلب بيانات المدينة بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/cities",
     *     summary="إضافة مدينة جديدة",
     *     tags={"Cities"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="حلب"),
     *             @OA\Property(property="description", type="string", example="مدينة صناعية وتجارية"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء المدينة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء المدينة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="name", type="string", example="حلب"),
     *                 @OA\Property(property="description", type="string", example="مدينة صناعية وتجارية"),
     *                 @OA\Property(property="is_active", type="boolean", example=true)
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreCityRequest $request)
    {
        $city = City::create($request->validated());

        return $this->successResponse(
            new CityResource($city),
            'تم إنشاء المدينة بنجاح',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/api/cities/{id}",
     *     summary="تحديث بيانات مدينة",
     *     tags={"Cities"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف المدينة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="حلب الجديدة"),
     *             @OA\Property(property="description", type="string", example="مركز اقتصادي"),
     *             @OA\Property(property="is_active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات المدينة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات المدينة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="حلب الجديدة"),
     *                 @OA\Property(property="description", type="string", example="مركز اقتصادي"),
     *                 @OA\Property(property="is_active", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="المدينة غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="المدينة غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateCityRequest $request, $id)
    {
        $city = City::find($id);

        if (!$city) {
            return $this->error('المدينة غير موجودة', 404);
        }

        $city->update($request->validated());

        return $this->successResponse(
            new CityResource($city),
            'تم تحديث بيانات المدينة بنجاح',
            200
        );
    }

   /**
 * @OA\Delete(
 *     path="/api/cities/{id}",
 *     summary="حذف مدينة (بعد التأكيد)",
 *     tags={"Cities"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدينة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="force",
 *         in="query",
 *         required=false,
 *         description="حذف قسري",
 *         @OA\Schema(type="boolean", example=false)
 *     ),
 *     @OA\Response(response=200, description="تم حذف المدينة")
 * )
 */
public function destroy(Request $request, $id)
{
    $city = City::find($id);

    if (!$city) {
        return $this->error('المدينة غير موجودة', 404);
    }

    if ($city->students()->exists() && !$request->boolean('force')) {
        return $this->error(
            'يجب تأكيد الحذف لوجود طلاب مرتبطين بالمدينة',
            409
        );
    }

    $city->delete();

    return $this->successResponse(
        null,
        'تم حذف المدينة بنجاح',
        200
    );
}

    /**
 * @OA\Get(
 *     path="/api/cities/{id}/delete-check",
 *     summary="فحص إمكانية حذف مدينة",
 *     tags={"Cities"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرف المدينة",
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
 *             @OA\Property(property="message", type="string", example="المدينة مرتبطة بطلاب")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="المدينة غير موجودة"
 *     )
 * )
 */
public function checkDelete($id)
{
    $city = City::find($id);

    if (!$city) {
        return $this->error('المدينة غير موجودة', 404);
    }

    $relations = [];

    if ($city->students()->exists()) {
        $relations[] = 'الطلاب';
    }

    if (!empty($relations)) {
        return $this->successResponse([
            'can_delete' => false,
            'relations'  => $relations,
            'message'    => 'لا يمكن حذف المدينة لوجود ارتباطات'
        ], 'تحذير قبل الحذف');
    }

    return $this->successResponse([
        'can_delete' => true,
        'relations'  => [],
        'message'    => 'يمكن حذف المدينة بأمان'
    ], 'لا توجد ارتباطات');
}

}
