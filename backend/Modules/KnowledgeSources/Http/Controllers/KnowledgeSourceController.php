<?php

namespace Modules\KnowledgeSources\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\KnowledgeSources\Models\KnowledgeSource;
use Modules\KnowledgeSources\Http\Requests\StoreKnowledgeSourceRequest;
use Modules\KnowledgeSources\Http\Requests\UpdateKnowledgeSourceRequest;
use Modules\KnowledgeSources\Http\Resources\KnowledgeSourceResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class KnowledgeSourceController extends Controller
{
    use SuccessResponseTrait;
/**
 * @OA\Get(
 *     path="/api/knowledge-sources",
 *     summary="جلب جميع طرق المعرفة",
 *     description="
 * يعيد هذا المسار جميع طرق المعرفة المسجلة في النظام من جدول `knowledge_sources`.
 *
 * 📌 **سلوكيات:**
 * - إذا لا توجد أي بيانات: يعيد 404 برسالة واضحة.
 * - يعيد البيانات ضمن المفتاح `data` كمصفوفة.
 * ",
 *     operationId="knowledgeSourcesIndex",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب جميع طرق المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب جميع طرق المعرفة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="فيسبوك"),
 *                     @OA\Property(property="description", type="string", nullable=true, example="وسائل التواصل الاجتماعي"),
 *                     @OA\Property(property="is_active", type="boolean", example=true),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="لا يوجد أي طرق معرفة مسجلة حالياً",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="لا يوجد أي طرق معرفة مسجلة حالياً"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function index()
    {
        $sources = KnowledgeSource::all();

        if ($sources->isEmpty()) {
            return $this->error(
                'لا يوجد أي طرق معرفة مسجلة حالياً',
                404
            );
        }

        return $this->successResponse(
            KnowledgeSourceResource::collection($sources),
            'تم جلب جميع طرق المعرفة بنجاح',
            200
        );
    }

  /**
 * @OA\Post(
 *     path="/api/knowledge-sources",
 *     summary="إضافة طريقة معرفة جديدة",
 *     description="
 * يقوم هذا المسار بإنشاء طريقة معرفة جديدة في جدول `knowledge_sources`.
 *
 * ✅ **ملاحظات:**
 * - حقل `name` إلزامي ويجب أن يكون فريدًا.
 * - الحقول `description` و `is_active` اختيارية.
 * - `is_active` الافتراضي true إذا لم يُرسل.
 * ",
 *     operationId="knowledgeSourcesStore",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="إعلان ورقي"),
 *             @OA\Property(property="description", type="string", nullable=true, example="منشورات مطبوعة"),
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="تم إضافة طريقة المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم إضافة طريقة المعرفة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="name", type="string", example="إعلان ورقي"),
 *                 @OA\Property(property="description", type="string", nullable=true, example="منشورات مطبوعة"),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="خطأ في التحقق من البيانات",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object", example={"name":{"حقل name مطلوب"}})
 *         )
 *     )
 * )
 */
    public function store(StoreKnowledgeSourceRequest $request)
    {
        $source = KnowledgeSource::create($request->validated());

        return $this->successResponse(
            new KnowledgeSourceResource($source),
            'تم إضافة طريقة المعرفة بنجاح',
            201
        );
    }

   /**
 * @OA\Get(
 *     path="/api/knowledge-sources/{id}",
 *     summary="عرض طريقة معرفة واحدة",
 *     description="يعيد تفاصيل طريقة معرفة واحدة من جدول `knowledge_sources` حسب المعرّف.",
 *     operationId="knowledgeSourcesShow",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرّف طريقة المعرفة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب طريقة المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب طريقة المعرفة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="فيسبوك"),
 *                 @OA\Property(property="description", type="string", nullable=true, example="وسائل التواصل الاجتماعي"),
 *                 @OA\Property(property="is_active", type="boolean", example=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="طريقة المعرفة غير موجودة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="طريقة المعرفة غير موجودة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function show($id)
    {
        $source = KnowledgeSource::find($id);

        if (!$source) {
            return $this->error(
                'طريقة المعرفة غير موجودة',
                404
            );
        }

        return $this->successResponse(
            new KnowledgeSourceResource($source),
            'تم جلب طريقة المعرفة بنجاح',
            200
        );
    }
/**
 * @OA\Put(
 *     path="/api/knowledge-sources/{id}",
 *     summary="تعديل طريقة معرفة",
 *     description="
 * يقوم هذا المسار بتعديل بيانات طريقة معرفة موجودة في جدول `knowledge_sources`.
 *
 * ✅ **ملاحظات:**
 * - يمكن إرسال حقل واحد فقط أو أكثر.
 * - `name` يجب أن يبقى فريدًا (مع تجاهل السجل الحالي).
 * ",
 *     operationId="knowledgeSourcesUpdate",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرّف طريقة المعرفة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="إعلان مطبوع"),
 *             @OA\Property(property="description", type="string", nullable=true, example="إعلانات ورقية"),
 *             @OA\Property(property="is_active", type="boolean", example=false)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم تعديل طريقة المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم تعديل طريقة المعرفة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="إعلان مطبوع"),
 *                 @OA\Property(property="description", type="string", nullable=true, example="إعلانات ورقية"),
 *                 @OA\Property(property="is_active", type="boolean", example=false),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-02T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="طريقة المعرفة غير موجودة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="طريقة المعرفة غير موجودة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="خطأ في التحقق من البيانات",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object", example={"name":{"قيمة name مستخدمة مسبقًا"}})
 *         )
 *     )
 * )
 */


    public function update(UpdateKnowledgeSourceRequest $request, $id)
    {
        $source = KnowledgeSource::find($id);

        if (!$source) {
            return $this->error(
                'طريقة المعرفة غير موجودة',
                404
            );
        }

        $source->update($request->validated());

        return $this->successResponse(
            new KnowledgeSourceResource($source),
            'تم تعديل طريقة المعرفة بنجاح',
            200
        );
    }

 /**
 * @OA\Delete(
 *     path="/api/knowledge-sources/{id}",
 *     summary="حذف طريقة معرفة",
 *     description="
 * يقوم هذا المسار بحذف طريقة المعرفة من جدول `knowledge_sources`.
 *
 * ⚠️ **تنبيه مهم:**
 * - إن كانت هذه القيمة مستخدمة في سجلات أخرى مستقبلًا (طلاب مثلاً)،
 *   قد تفضّل تعطيلها بدل الحذف عبر مسار toggle.
 * ",
 *     operationId="knowledgeSourcesDestroy",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرّف طريقة المعرفة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم حذف طريقة المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم حذف طريقة المعرفة بنجاح"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="طريقة المعرفة غير موجودة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="طريقة المعرفة غير موجودة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */

    public function destroy($id)
    {
        $source = KnowledgeSource::find($id);

        if (!$source) {
            return $this->error(
                'طريقة المعرفة غير موجودة',
                404
            );
        }

        $source->delete();

        return $this->successResponse(
            null,
            'تم حذف طريقة المعرفة بنجاح',
            200
        );
    }
/**
 * @OA\Patch(
 *     path="/api/knowledge-sources/{id}/toggle",
 *     summary="تفعيل أو تعطيل طريقة معرفة",
 *     description="
 * يقوم هذا المسار بتبديل حالة `is_active` لطريقة المعرفة:
 * - إذا كانت true تصبح false
 * - إذا كانت false تصبح true
 * ",
 *     operationId="knowledgeSourcesToggleStatus",
 *     tags={"KnowledgeSources"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="معرّف طريقة المعرفة",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم تحديث حالة طريقة المعرفة بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم تحديث حالة طريقة المعرفة بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="فيسبوك"),
 *                 @OA\Property(property="description", type="string", nullable=true, example="وسائل التواصل الاجتماعي"),
 *                 @OA\Property(property="is_active", type="boolean", example=false),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01T00:00:00.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-02T00:00:00.000000Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="طريقة المعرفة غير موجودة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="طريقة المعرفة غير موجودة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */


    public function toggleStatus($id)
    {
        $source = KnowledgeSource::find($id);

        if (!$source) {
            return $this->error(
                'طريقة المعرفة غير موجودة',
                404
            );
        }

        $source->is_active = !$source->is_active;
        $source->save();

        return $this->successResponse(
            new KnowledgeSourceResource($source),
            'تم تحديث حالة طريقة المعرفة بنجاح',
            200
        );
    }
}
