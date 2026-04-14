<?php

namespace Modules\Guardians\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Guardians\Models\Guardian;
use Modules\Guardians\Http\Requests\StoreGuardianRequest;
use Modules\Guardians\Http\Requests\UpdateGuardianRequest;
use Modules\Guardians\Http\Resources\GuardianResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class GuardiansController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/guardians",
     *     summary="قائمة جميع أولياء الأمور",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع أولياء الأمور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع أولياء الأمور بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="family_id", type="integer", example=1),
     *                     @OA\Property(property="first_name", type="string", example="أحمد"),
     *                     @OA\Property(property="last_name", type="string", example="محمد"),
     *                     @OA\Property(property="national_id", type="string", example="123456789"),
     *                     @OA\Property(property="is_primary_contact", type="boolean", example=true),
     *                     @OA\Property(property="occupation", type="string", example="مهندس"),
     *                     @OA\Property(property="address", type="string", example="دمشق، المزة"),
     *                     @OA\Property(property="relationship", type="string", example="father"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد أولياء أمور",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي ولي أمر مسجل حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    //  public function index()
    // {
    //     try {
    //         $guardians = Guardian::all();

    //         if ($guardians->isEmpty()) {
    //             return $this->error('لا يوجد أي ولي أمر مسجل حالياً', 404);
    //         }

    //         return $this->successResponse(
    //             GuardianResource::collection($guardians),
    //             'تم جلب جميع أولياء الأمور بنجاح',
    //             200
    //         );

    //     } catch (\Illuminate\Database\QueryException $e) {
    //         // أخطاء قاعدة البيانات
    //         Log::error('خطأ في قاعدة البيانات أثناء جلب أولياء الأمور', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return $this->error('حدث خطأ في قاعدة البيانات أثناء جلب البيانات', 500);

    //     } catch (\Exception $e) {
    //         // أي خطأ عام أو أثناء فك التشفير
    //         Log::error('حدث خطأ غير متوقع في index (GuardianController)', [
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString(),
    //         ]);
    //         return $this->error('حدث خطأ غير متوقع أثناء جلب أولياء الأمور، يرجى المحاولة لاحقاً', 500);
    //     }
    // }
    //سبب الكود وجود بيانات قديمة غير مشفرة
    public function index()
    {
        try {
            $guardians = Guardian::all();

            if ($guardians->isEmpty()) {
                return $this->error('لا يوجد أي ولي أمر مسجل حالياً', 404);
            }

            // نحاول تمرير البيانات عبر الـ Resource
            try {
                return $this->successResponse(
                    GuardianResource::collection($guardians),
                    'تم جلب جميع أولياء الأمور بنجاح',
                    200
                );
            } catch (\Exception $e) {
                // في حال فشل فك التشفير داخل الـ Resource أو الـ Model
                $safeGuardians = $guardians->map(function ($guardian) {
                    try {
                        // نحاول الوصول للحقول، إن فشلنا نعيدها كنص خام
                        $guardian->first_name = $guardian->first_name ?? $guardian->getRawOriginal('first_name');
                        $guardian->last_name = $guardian->last_name ?? $guardian->getRawOriginal('last_name');
                        $guardian->national_id = $guardian->national_id ?? $guardian->getRawOriginal('national_id');
                    } catch (\Exception $inner) {
                        // في حالة الخطأ نعيد القيم الخام من قاعدة البيانات
                        $guardian->first_name = $guardian->getRawOriginal('first_name');
                        $guardian->last_name = $guardian->getRawOriginal('last_name');
                        $guardian->national_id = $guardian->getRawOriginal('national_id');
                    }
                    return $guardian;
                });

                return $this->successResponse(
                    GuardianResource::collection($safeGuardians),
                    'تم جلب أولياء الأمور، مع تخطي بعض السجلات التي تحتوي بيانات غير مشفرة',
                    206 // Partial Content
                );
            }
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب أولياء الأمور: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/guardians",
     *     summary="إضافة ولي أمر جديد",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name","password"},
     *             @OA\Property(property="family_id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="أحمد"),
     *             @OA\Property(property="last_name", type="string", example="محمد"),
     *             @OA\Property(property="national_id", type="string", example="123456789"),
     *             @OA\Property(property="is_primary_contact", type="boolean", example=true),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="occupation", type="string", example="مهندس"),
     *             @OA\Property(property="address", type="string", example="دمشق، المزة"),
     *             @OA\Property(property="relationship", type="string", example="father")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء ولي الأمر بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء ولي الأمر بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="family_id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="أحمد"),
     *                 @OA\Property(property="last_name", type="string", example="محمد"),
     *                 @OA\Property(property="national_id", type="string", example="123456789"),
     *                 @OA\Property(property="is_primary_contact", type="boolean", example=true),
     *                 @OA\Property(property="occupation", type="string", example="مهندس"),
     *                 @OA\Property(property="address", type="string", example="دمشق، المزة"),
     *                 @OA\Property(property="relationship", type="string", example="father"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreGuardianRequest $request)
    {
        $guardian = Guardian::create($request->validated());

        return $this->successResponse(
            new GuardianResource($guardian),
            'تم إنشاء ولي الأمر بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/guardians/{id}",
     *     summary="عرض تفاصيل ولي أمر محدد",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف ولي الأمر",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات ولي الأمر بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات ولي الأمر بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="family_id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="أحمد"),
     *                 @OA\Property(property="last_name", type="string", example="محمد"),
     *                 @OA\Property(property="national_id", type="string", example="123456789"),
     *                 @OA\Property(property="phone", type="string", example="+963123456789"),
     *                 @OA\Property(property="is_primary_contact", type="boolean", example=true),
     *                 @OA\Property(property="occupation", type="string", example="مهندس"),
     *                 @OA\Property(property="address", type="string", example="دمشق، المزة"),
     *                 @OA\Property(property="relationship", type="string", example="father"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ولي الأمر غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="ولي الأمر غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $guardian = Guardian::find($id);

        if (!$guardian) {
            return $this->error('ولي الأمر غير موجود', 404);
        }

        return $this->successResponse(
            new GuardianResource($guardian),
            'تم جلب بيانات ولي الأمر بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/guardians/{id}",
     *     summary="تحديث بيانات ولي أمر",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف ولي الأمر",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="family_id", type="integer", example=2),
     *             @OA\Property(property="first_name", type="string", example="محمد"),
     *             @OA\Property(property="last_name", type="string", example="علي"),
     *             @OA\Property(property="national_id", type="string", example="987654321"),
     
     *             @OA\Property(property="is_primary_contact", type="boolean", example=false),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="occupation", type="string", example="معلم"),
     *             @OA\Property(property="address", type="string", example="حلب، الشهباء"),
     *             @OA\Property(property="relationship", type="string", example="mother")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات ولي الأمر بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات ولي الأمر بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="family_id", type="integer", example=2),
     *                 @OA\Property(property="first_name", type="string", example="محمد"),
     *                 @OA\Property(property="last_name", type="string", example="علي"),
     *                 @OA\Property(property="national_id", type="string", example="987654321"),
    
     *                 @OA\Property(property="is_primary_contact", type="boolean", example=false),
     *                 @OA\Property(property="occupation", type="string", example="معلم"),
     *                 @OA\Property(property="address", type="string", example="حلب، الشهباء"),
     *                 @OA\Property(property="relationship", type="string", example="mother"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ولي الأمر غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="ولي الأمر غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateGuardianRequest $request, $id)
    {
        $guardian = Guardian::find($id);

        if (!$guardian) {
            return $this->error('ولي الأمر غير موجود', 404);
        }

        $guardian->update($request->validated());

        return $this->successResponse(
            new GuardianResource($guardian),
            'تم تحديث بيانات ولي الأمر بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/guardians/{id}",
     *     summary="حذف ولي أمر",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف ولي الأمر",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف ولي الأمر بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف ولي الأمر بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="ولي الأمر غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="ولي الأمر غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $guardian = Guardian::find($id);

        if (!$guardian) {
            return $this->error('ولي الأمر غير موجود', 404);
        }

        $guardian->delete();

        return $this->successResponse(
            null,
            'تم حذف ولي الأمر بنجاح',
            200
        );
    }
    
    /**
     * @OA\Get(
     *     path="/api/guardians/total-guardians",
     *     summary="عدد أولياء الأمور الكلي",
     *     tags={"Guardians"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب عدد أولياء الأمور بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب عدد أولياء الأمور بنجاح"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_guardians", type="integer", example=1200)
     *             )
     *         )
     *     )
     * )
     */
    public function totalGuardians()
    {
        $count = Guardian::count();

        return $this->successResponse(
            ['total_guardians' => $count],
            'تم جلب عدد أولياء الأمور بنجاح',
            200
        );
    }

}

