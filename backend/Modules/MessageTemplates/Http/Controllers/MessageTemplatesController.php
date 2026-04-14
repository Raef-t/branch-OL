<?php

namespace Modules\MessageTemplates\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MessageTemplates\Models\MessageTemplate;
use Modules\MessageTemplates\Http\Requests\StoreMessageTemplateRequest;
use Modules\MessageTemplates\Http\Requests\UpdateMessageTemplateRequest;
use Modules\MessageTemplates\Http\Resources\MessageTemplateResource;
use Modules\Shared\Traits\SuccessResponseTrait;

class MessageTemplatesController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/message-templates",
     *     summary="قائمة جميع قوالب الرسائل",
     *     description="جلب جميع قوالب الرسائل مع إمكانية الفلترة حسب قناة الإرسال، التصنيف، وحالة التفعيل",
     *     tags={"MessageTemplates"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="قناة الإرسال (sms,in_app,email)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="sms"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="تصنيف الرسالة (مثل: payments, attendance, exams, general)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="payments"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="فلترة حسب حالة التفعيل",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean",
     *             example=true
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب قوالب الرسائل بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب قوالب الرسائل بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="تنبيه دفعة متأخرة"),
     *                     @OA\Property(property="type", type="string", example="sms"),
     *                     @OA\Property(property="category", type="string", example="payments"),
     *                     @OA\Property(property="subject", type="string", example="تذكير بالدفعة"),
     *                     @OA\Property(
     *                         property="body",
     *                         type="string",
     *                         example="مرحبًا {student_name}، يرجى دفع القسط المستحق بتاريخ {due_date}."
     *                     ),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد قوالب رسائل مطابقة للبحث",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي قالب رسالة مطابق للبحث"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = MessageTemplate::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->latest()->get();

        if ($templates->isEmpty()) {
            return $this->error('لا يوجد أي قالب رسالة مطابق للبحث', 404);
        }

        return $this->successResponse(
            MessageTemplateResource::collection($templates),
            'تم جلب قوالب الرسائل بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/message-templates",
     *     summary="إضافة قالب رسالة جديد",
     *     tags={"MessageTemplates"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات إنشاء قالب الرسالة",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/MessageTemplateStoreRequest"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء قالب الرسالة بنجاح"
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات"
     *     )
     * )
     */

    public function store(StoreMessageTemplateRequest $request)
    {
        $template = MessageTemplate::create($request->validated());

        return $this->successResponse(
            new MessageTemplateResource($template),
            'تم إنشاء قالب الرسالة بنجاح',
            201
        );
    }


    /**
     * @OA\Get(
     *     path="/api/message-templates/{id}",
     *     summary="عرض تفاصيل قالب رسالة محدد",
     *     tags={"MessageTemplates"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قالب الرسالة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات قالب الرسالة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات قالب الرسالة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/MessageTemplateResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="قالب الرسالة غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قالب الرسالة غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        $template = MessageTemplate::find($id);

        if (!$template) {
            return $this->error('قالب الرسالة غير موجود', 404);
        }

        return $this->successResponse(
            new MessageTemplateResource($template),
            'تم جلب بيانات قالب الرسالة بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/message-templates/{id}",
     *     summary="تحديث بيانات قالب رسالة",
     *     tags={"MessageTemplates"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قالب الرسالة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="بيانات التحديث (يمكن تحديث أي حقل مسموح)",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/MessageTemplateStoreRequest"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات قالب الرسالة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات قالب الرسالة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/MessageTemplateResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="قالب الرسالة غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قالب الرسالة غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ValidationErrorResponse"
     *         )
     *     )
     * )
     */

    public function update(UpdateMessageTemplateRequest $request, $id)
    {
        $template = MessageTemplate::find($id);

        if (!$template) {
            return $this->error('قالب الرسالة غير موجود', 404);
        }

        $template->update($request->validated());

        return $this->successResponse(
            new MessageTemplateResource($template),
            'تم تحديث بيانات قالب الرسالة بنجاح',
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/message-templates/{id}",
     *     summary="حذف قالب رسالة",
     *     tags={"MessageTemplates"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف قالب الرسالة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف قالب الرسالة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف قالب الرسالة بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="قالب الرسالة غير موجود",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="قالب الرسالة غير موجود"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $template = MessageTemplate::find($id);

        if (!$template) {
            return $this->error('قالب الرسالة غير موجود', 404);
        }

        $template->delete();

        return $this->successResponse(
            null,
            'تم حذف قالب الرسالة بنجاح',
            200
        );
    }
}
