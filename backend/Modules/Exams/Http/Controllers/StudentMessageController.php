<?php

namespace Modules\Exams\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exams\Services\StudentMessageService;

class StudentMessageController extends Controller
{
    protected $service;

    public function __construct(StudentMessageService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Post(
     *     path="/api/exams/student-messages",
     *     summary="تسجيل الرسائل المرسلة للطلاب",
     *     description="
     *     تُستخدم هذه الواجهة لتسجيل جميع الرسائل المرسلة لمجموعة من الطلاب. 
     *     يتم تمرير مصفوفة الـIDs الخاصة بالطلاب، ويمكن تحديد قالب الرسالة المستخدم (اختياري). 
     *     ",
     *     tags={"Exams"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="student_ids",
     *                 type="array",
     *                 description="مصفوفة الـIDs الخاصة بالطلاب الذين سيتم تسجيل الرسائل لهم",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *             @OA\Property(
     *                 property="template_id",
     *                 type="integer",
     *                 nullable=true,
     *                 description="رقم قالب الرسالة المستخدم لهذه الرسائل (اختياري)",
     *                 example=3
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم تسجيل الرسائل بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تسجيل الرسائل بنجاح."),
     *             @OA\Property(property="count", type="integer", example=5, description="عدد الطلاب الذين تم تسجيل الرسائل لهم")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في التحقق من البيانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "student_ids": {"بعض الـIDs غير موجودة في قاعدة البيانات"}
     *                 }
     *             )
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'template_id' => 'nullable|exists:message_templates,id',
        ]);

        $count = $this->service->storeMessages($request->student_ids, $request->template_id);

        return response()->json([
            'message' => 'تم تسجيل الرسائل بنجاح.',
            'count' => $count,
        ]);
    }
}
