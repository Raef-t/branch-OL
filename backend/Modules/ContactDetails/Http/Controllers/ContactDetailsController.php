<?php

namespace Modules\ContactDetails\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ContactDetails\Models\ContactDetail;
use Modules\ContactDetails\Http\Requests\StoreContactDetailRequest;
use Modules\ContactDetails\Http\Requests\UpdateContactDetailRequest;
use Modules\ContactDetails\Http\Resources\ContactDetailResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Models\Student;
use Modules\Guardians\Models\Guardian;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="ContactDetails",
 *     description="إدارة تفاصيل الاتصال لأولياء الأمور (هواتف، بريد إلكتروني، عناوين، واتساب)"
 * )
 */
class ContactDetailsController extends Controller
{
    use SuccessResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/contact-details",
     *     summary="قائمة جميع تفاصيل الاتصال",
     *     tags={"ContactDetails"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع تفاصيل الاتصال بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع تفاصيل الاتصال بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ContactDetailResource")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد تفاصيل اتصال",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي تفاصيل اتصال مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    
    public function index(Request $request)
    {
        $query = ContactDetail::query();

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('family_id')) {
            $query->where('family_id', $request->family_id);
        }

        if ($request->has('guardian_id')) {
            $query->where('guardian_id', $request->guardian_id);
        }

        $contactDetails = $query->get();

        if ($contactDetails->isEmpty()) {
            return $this->error('لا يوجد أي تفاصيل اتصال مسجلة لمحددات البحث الحالية', 404);
        }

        return $this->successResponse(
            ContactDetailResource::collection($contactDetails),
            'تم جلب تفاصيل الاتصال بنجاح',
            200
        );
    }

   /**
 * @OA\Post(
 *   path="/api/contact-details",
 *   summary="إضافة تفاصيل اتصال جديدة",
 *   description="يتم إنشاء سجل اتصال جديد. يرجى الملاحظة:
 *
 * **1) قواعد النوع (Type Rules):**
 * - إذا كان `type = phone`: يجب إرسال `country_code` و `phone_number` (وسيتم تجاهل `value`).
 * - إذا كان `type = landline`: يجب إرسال `phone_number`ويمكن إرسال `country_code` كرمز مدينة (وسيتم تجاهل `value`).
 * - إذا كان `type` غير ذلك: يجب إرسال الحقل `value` (وسيتم تجاهل حقول الهاتف).
 *
 * **2) قواعد الملكية (Ownership Rules):**
 * - يجب إرسال ID يعبر عن صاحب الرقم بناءً على `owner_type`:
 *   - إذا كان المالك أب أو أم (`father`, `mother`): يجب إرسال `guardian_id`.
 *   - إذا كان المالك طالب (`student`): يجب إرسال `student_id`.
 *   - إذا كان المالك أخ، أو قريب، أو آخر، أو العائلة (`sibling`, `relative`, `other`, `family`) أو كان النوع `landline`: يجب إرسال `family_id`.
 * - حقل `owner_name` يمكن إرساله كاسم توضيحي، لكنه لا يُغني عن إرسال الـ ID.
 *
 * **3) قواعد الاستخدام (Usage Rules):**
 * - للأرقام (phone)، يجب تفعيل خيار واحد على الأقل من: `supports_call`, `supports_whatsapp`, `supports_sms`.
 * - للـ `landline` يتم تلقائياً تفعيل `supports_call` وتعطيل الباقي.
 * - **الرقم الأساسي (is_primary):** يُسمح فقط للهاتف المحمول (`phone`) الذي يُفعل خيار استقبال الرسائل (`supports_sms`=true).
 * - بتعيين رقم كـ `is_primary`، يتم إلغاء هذا التعيين عن باقي أرقام نفس الشخص.
 *
 * **4) نوع المالك (Owner Type):**
 * - يحدد الصلة: `father`, `mother`, `student`, `sibling`, `relative`, `other`, `family`.",
 *   tags={"ContactDetails"},
 *   security={{"sanctum":{}}},
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"type"},
 *       @OA\Property(property="guardian_id", type="integer", example=1, description="معرف ولي الأمر - يربط الرقم بولي أمر محدد"),
 *       @OA\Property(property="student_id", type="integer", example=1, description="معرف الطالب - يربط الرقم بطالب محدد"),
 *       @OA\Property(property="family_id", type="integer", example=1, description="معرف العائلة - يربط الرقم بملف عائلة كامل"),
 *       @OA\Property(
 *         property="type",
 *         type="string",
 *         enum={"phone","email","address","whatsapp","landline"},
 *         example="phone",
 *         description="نوع جهة الاتصال (phone, email, address, whatsapp, landline)"
 *       ),
 *       @OA\Property(property="value", type="string", example="example@email.com", description="القيمة النصية (مطلوبة إذا كان النوع ليس phone)"),
 *       @OA\Property(property="country_code", type="string", example="+963", description="رمز الدولة أو المدينة (مطلوب لنوع phone، واختياري للـ landline كرمز محلي)"),
 *       @OA\Property(property="phone_number", type="string", example="912345678", description="رقم الهاتف بدون أصفار أو رمز (مطلوب فقط لنوع phone)"),
 *       @OA\Property(property="owner_type", type="string", enum={"father","mother","student","sibling","relative","other"}, example="father", description="صلة القرابة لصاحب الرقم"),
 *       @OA\Property(property="owner_name", type="string", example="أحمد محمد", description="اسم صاحب الرقم التوضيحي (اختياري)"),
 *       @OA\Property(property="supports_call", type="boolean", example=true, description="هل الرقم يدعم الاتصال التقليدي؟"),
 *       @OA\Property(property="supports_whatsapp", type="boolean", example=true, description="هل الرقم مسجل في واتساب؟"),
 *       @OA\Property(property="supports_sms", type="boolean", example=true, description="هل يدعم استقبال رسائل SMS؟ (إلزامي للرقم الأساسي)"),
 *       @OA\Property(property="is_primary", type="boolean", example=true, description="هل هذه هي وسيلة التواصل الأساسية؟ (يسمح فقط لنوع phone الداعم للـ SMS)"),
 *       @OA\Property(property="notes", type="string", example="للاستخدام العاجل فقط", nullable=true, description="أي ملاحظات إضافية")
 *     )
 *   ),
 *   @OA\Response(
 *     response=201,
 *     description="تم إنشاء تفاصيل الاتصال بنجاح",
 *     @OA\JsonContent(
 *       @OA\Property(property="success", type="boolean", example=true),
 *       @OA\Property(property="message", type="string", example="تم إنشاء تفاصيل الاتصال بنجاح"),
 *       @OA\Property(property="data", ref="#/components/schemas/ContactDetailResource")
 *     )
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="خطأ في التحقق من البيانات - راجع قسم 'errors' للتفاصيل",
 *     @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *   ),
 *   @OA\Response(
 *     response=401,
 *     description="غير مصرح (تحتاج تسجيل دخول)",
 *     @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
 *   )
 * )
 */
    public function store(StoreContactDetailRequest $request)
    {
        $contactDetail = ContactDetail::create($request->validated());

        $message = 'تم إنشاء تفاصيل الاتصال بنجاح';
        if (!empty($contactDetail->oldPrimaryNumbersReplaced)) {
            $replacedNumbers = implode(' و ', $contactDetail->oldPrimaryNumbersReplaced);
            $message .= " وتم نزع صفة الأساسي للـ SMS عن الرقم/الأرقام ({$replacedNumbers}) ونقلها لهذا الرقم كونه يمثل العائلة.";
        }

        return $this->successResponse(
            new ContactDetailResource($contactDetail),
            $message,
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/contact-details/{contactDetail}",
     *     summary="عرض تفاصيل اتصال محددة",
     *     tags={"ContactDetails"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="contactDetail",
     *         in="path",
     *         required=true,
     *         description="معرف تفاصيل الاتصال",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات تفاصيل الاتصال بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات تفاصيل الاتصال بنجاح"),
     *             @OA\Property(property="data", ref="#/components/schemas/ContactDetailResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="تفاصيل الاتصال غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="تفاصيل الاتصال غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function show(ContactDetail $contactDetail)
    {
        return $this->successResponse(
            new ContactDetailResource($contactDetail),
            'تم جلب بيانات تفاصيل الاتصال بنجاح',
            200
        );
    }
/**
 * @OA\Put(
 *     path="/api/contact-details/{contactDetail}",
 *     summary="تحديث تفاصيل اتصال",
 *     description="تحديث سجل موجود (Patch Behavior).\n\n### ملاحظات هامة للفرونت إند:\n1. **عند تغيير النوع**: تتغير الحقول المطلوبة (للهاتف المحمول يلزم country_code و phone_number، وللأرضي يلزم phone_number ويمكن إرسال country_code كرمز مقاطعة).\n2. **للهاتف الأرضي (landline)**: الغرض الافتراضي هو اتصال فقط، ويشترط ربطه بعائلة (family_id).\n3. **الرقم الأساسي**: محصور بهاتف محمول يدعم الرسائل القصيرة. وعند تعيينه، يُلغى عن بقية أرقام نفس المالك.\n4. **تغيير المالك**: يمكن تغيير الجهة المرتبطة بها الوسيلة.",
 *     tags={"ContactDetails"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="contactDetail",
 *         in="path",
 *         required=true,
 *         description="المعرف الفريد لسجل الاتصال المراد تحديثه",   
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=false,
 *         @OA\JsonContent(
 *             @OA\Property(property="guardian_id", type="integer", example=2, description="تغيير ولي الأمر المرتبط"),
 *             @OA\Property(property="student_id", type="integer", example=1, description="تغيير الطالب المرتبط"),
 *             @OA\Property(property="family_id", type="integer", example=1, description="تغيير العائلة المرتبطة"),
 *             @OA\Property(
 *                 property="type",
 *                 type="string",
 *                 enum={"phone","email","address","whatsapp","landline"},
 *                 example="whatsapp",
 *                 description="تغيير نوع السجل (سيؤثر على الحقول المطلوبة)"
 *             ),
 *             @OA\Property(property="value", type="string", example="new-email@test.com", description="القيمة الجديدة", nullable=true),
 *             @OA\Property(property="country_code", type="string", example="+963", description="رمز الدولة الجديد", nullable=true),
 *             @OA\Property(property="phone_number", type="string", example="987654321", description="رقم الهاتف الجديد", nullable=true),
 *             @OA\Property(property="owner_type", type="string", enum={"father", "mother", "student", "sibling", "relative", "other"}, example="mother", description="تعديل صلة القرابة"),
 *             @OA\Property(property="owner_name", type="string", example="سعاد محمد", description="تعديل اسم صاحب الرقم"),
 *             @OA\Property(property="supports_call", type="boolean", example=true, description="تعديل دعم الاتصال"),
 *             @OA\Property(property="supports_whatsapp", type="boolean", example=true, description="تعديل دعم واتساب"),
 *             @OA\Property(property="supports_sms", type="boolean", example=false, description="تعديل دعم الرسائل"),
 *             @OA\Property(property="is_primary", type="boolean", example=true, description="هل يصبح هذا السجل هو الأساسي؟"),
 *             @OA\Property(property="notes", type="string", example="تم التحديث بناء على طلب الولي", description="تعديل الملاحظات", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="تم التحديث بنجاح، السجل الجديد مرفق في الـ data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم تحديث تفاصيل الاتصال بنجاح"),
 *             @OA\Property(property="data", ref="#/components/schemas/ContactDetailResource")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="المعرف (ID) غير موجود في قاعدة البيانات",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="تفاصيل الاتصال غير موجودة")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="البيانات المرسلة غير منطقية أو تخالف الشروط (مثلاً: رقم هاتف بدون كود دولة)",
 *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
 *     )
 * )
 */
public function update(UpdateContactDetailRequest $request, $contactDetail)
{
    // Handle cases where route model binding might fail
    if (!($contactDetail instanceof ContactDetail)) {
        $id = $request->route('contactDetail') ?? $request->route('contact_detail');
        $contactDetail = ContactDetail::findOrFail($id);
    }

    $userId = auth()->id();
    $oldData = $contactDetail->toArray();
    $newData = $request->validated();
    
    \Illuminate\Support\Facades\Log::debug("User {$userId} attempting to update Contact #{$contactDetail->id}", [
        'old' => $oldData,
        'payload' => $newData
    ]);

    $updated = $contactDetail->update($newData);
    
    $fresh = $contactDetail->fresh();
    
    \Illuminate\Support\Facades\Log::debug("Update result for Contact #{$contactDetail->id}: " . ($updated ? 'SUCCESS' : 'FAILED'), [
        'changes' => $contactDetail->getChanges(),
        'exists_after_save' => !is_null($fresh),
        'fresh_data' => $fresh ? $fresh->toArray() : 'NOT_FOUND_IN_DB'
    ]);

    $message = 'تم تحديث تفاصيل الاتصال بنجاح';
    if (!empty($contactDetail->oldPrimaryNumbersReplaced)) {
        $replacedNumbers = implode(' و ', $contactDetail->oldPrimaryNumbersReplaced);
        $message .= " وتم نزع صفة الأساسي للـ SMS عن الرقم/الأرقام ({$replacedNumbers}) ونقلها لهذا الرقم كونه يمثل العائلة.";
    }

    return $this->successResponse(
        new ContactDetailResource($contactDetail),
        $message,
        200
    );
}


    /**
     * @OA\Delete(
     *     path="/api/contact-details/{contactDetail}",
     *     summary="حذف تفاصيل اتصال",
     *     tags={"ContactDetails"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="contactDetail",
     *         in="path",
     *         required=true,
     *         description="معرف تفاصيل الاتصال",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف تفاصيل الاتصال بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم حذف تفاصيل الاتصال بنجاح"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="تفاصيل الاتصال غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="تفاصيل الاتصال غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
     *     )
     * )
     */
    public function destroy(ContactDetail $contactDetail)
    {
        $contactDetail->delete();

        return $this->successResponse(
            null,
            'تم حذف تفاصيل الاتصال بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/contact-details/student/{student_id}",
     *     summary="ملخص شامل لبيانات التواصل للطالب وعائلته وأولياء أمره",
     *     description="هذا الراوت يجمع كافة البيانات المبعثرة (بيانات الطالب الشخصية، أرقام العائلة والأرضي، أرقام الآباء والأمهات) ويرجعها في هيكل واحد مفصل لتسهيل العرض في الواجهات.",
     *     tags={"ContactDetails"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         required=true,
     *         description="معرف الطالب (ID)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب ملخص بيانات التواصل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب ملخص البيانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="full_family_summary", type="object",
     *                     @OA\Property(property="student_name", type="string"),
     *                     @OA\Property(property="family_id", type="integer"),
     *                     @OA\Property(property="primary_sms_contact", type="object", description="هذا هو الرقم المعتمد في النظام لإرسال الرسائل التلقائية للعائلة")
     *                 ),
     *                 @OA\Property(property="personal_contacts", type="array", @OA\Items(ref="#/components/schemas/ContactDetailResource")),
     *                 @OA\Property(property="family_contacts", type="array", @OA\Items(ref="#/components/schemas/ContactDetailResource")),
     *                 @OA\Property(property="guardians_contacts", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="guardian_id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="relationship", type="string"),
     *                         @OA\Property(property="legacy_phone", type="string", description="رقم الهاتف الأساسي المسجل في جدول الأوصياء"),
     *                         @OA\Property(property="details", type="array", @OA\Items(ref="#/components/schemas/ContactDetailResource"))
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getStudentContactsSummary($studentId)
    {
        // 1. جلب الطالب مع عائلته وأوصيائه
        $student = Student::with(['family.guardians'])->find($studentId);

        if (!$student) {
            return $this->error('الطالب غير موجود', 404);
        }

        $familyId = $student->family_id;
        $guardianIds = $student->family ? $student->family->guardians->pluck('id')->toArray() : [];

        // 2. جلب كافة وسائل التواصل المرتبطة بكل الأطراف
        $allContacts = ContactDetail::where('student_id', $studentId)
            ->orWhere('family_id', $familyId)
            ->orWhereIn('guardian_id', $guardianIds)
            ->orderBy('is_primary', 'desc')
            ->get();

        // 3. بناء الهيكل المفصل بشكل يمنع التكرار (Mutual Exclusivity)
        // الأرقام الشخصية للطالب فقط
        $personalContacts = $allContacts->where('owner_type', 'student');
        
        // أرقام العائلة (أرضي، أخوة، أقارب، أو بند "العائلة" العام) - نستبعد منها الطالب والآباء
        $familyContacts = $allContacts->whereNotIn('owner_type', ['student', 'father', 'mother']);
        
        // الرقم الأساسي المعتمد للـ SMS
        $primarySms = $allContacts->firstWhere('is_primary', true);

        $data = [
            'full_family_summary' => [
                'student_id'   => $student->id,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'family_id'    => $familyId,
                'primary_sms_contact' => $primarySms ? new ContactDetailResource($primarySms) : null,
            ],
            'personal_contacts' => ContactDetailResource::collection($personalContacts),
            'family_contacts'   => ContactDetailResource::collection($familyContacts),
            'guardians_contacts' => $student->family ? $student->family->guardians->map(function ($guardian) use ($allContacts) {
                // هنا نأخذ فقط الأرقام التي تخص هذا الولي (أب أو أم)
                return [
                    'guardian_id'  => $guardian->id,
                    'name'         => $guardian->first_name . ' ' . $guardian->last_name,
                    'relationship' => $guardian->relationship,
                    'legacy_phone' => $guardian->phone,
                    'details'      => ContactDetailResource::collection($allContacts->where('guardian_id', $guardian->id)),
                ];
            }) : []
        ];

        return $this->successResponse($data, 'تم جلب ملخص بيانات التواصل بنجاح');
    }
}