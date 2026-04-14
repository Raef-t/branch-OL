<?php

namespace Modules\ExamResults\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ExamResults\Models\ExamResult;
use Modules\ExamResults\Http\Requests\StoreExamResultRequest;
use Modules\ExamResults\Http\Requests\UpdateExamResultRequest;
use Modules\ExamResults\Http\Resources\ExamResultResource;
use Modules\Shared\Traits\SuccessResponseTrait;
use Modules\Students\Models\Student;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Attendances\Models\Attendance;
use Modules\ExamResultEditRequests\Models\ExamResultEditRequest;
use Modules\ExamResults\Http\Resources\ExamResultEditRequestResource;
use Modules\ExamResults\Http\Requests\FilterExamResultsRequest;
use Modules\ExamResults\Http\Resources\ExamResultDetailResource;
use Modules\ExamResults\Services\ExamResultFilterService;
use Modules\Exams\Models\Exam;

class ExamResultsController extends Controller
{
    use SuccessResponseTrait;
    protected $notificationService;
    protected ExamResultFilterService $examResultFilterService;
    public function __construct(NotificationService $notificationService, ExamResultFilterService $examResultFilterService)
    {
        $this->notificationService = $notificationService;
        $this->examResultFilterService = $examResultFilterService;
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results",
     *     summary="قائمة جميع نتائج الامتحانات",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب جميع نتائج الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع نتائج الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="exam_id", type="integer", example=1),
     *                     @OA\Property(property="student_id", type="integer", example=1),
     *                     @OA\Property(property="obtained_marks", type="number", format="float", example=85.50),
     *                     @OA\Property(property="is_passed", type="boolean", example=true),
     *                     @OA\Property(property="remarks", type="string", example="ممتاز"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="لا يوجد نتائج امتحانات",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا يوجد أي نتيجة امتحان مسجلة حالياً"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $examResults = ExamResult::all();

        if ($examResults->isEmpty()) {
            return $this->error('لا يوجد أي نتيجة امتحان مسجلة حالياً', 404);
        }

        return $this->successResponse(
            ExamResultResource::collection($examResults),
            'تم جلب جميع نتائج الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/exam-results",
     *     summary="إضافة نتيجة امتحان جديدة",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"exam_id","student_id","obtained_marks"},
     *             @OA\Property(property="exam_id", type="integer", example=1),
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="obtained_marks", type="number", format="float", example=85.50),
     *             @OA\Property(property="is_passed", type="boolean", example=true),
     *             @OA\Property(property="remarks", type="string", example="ممتاز")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء نتيجة الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم إنشاء نتيجة الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="exam_id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="obtained_marks", type="number", format="float", example=85.50),
     *                 @OA\Property(property="is_passed", type="boolean", example=true),
     *                 @OA\Property(property="remarks", type="string", example="ممتاز"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreExamResultRequest $request)
    {
        $examResult = ExamResult::create($request->validated());

        return $this->successResponse(
            new ExamResultResource($examResult),
            'تم إنشاء نتيجة الامتحان بنجاح',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/{id}",
     *     summary="عرض تفاصيل نتيجة امتحان محددة",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف نتيجة الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب بيانات نتيجة الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب بيانات نتيجة الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="exam_id", type="integer", example=1),
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="obtained_marks", type="number", format="float", example=85.50),
     *                 @OA\Property(property="is_passed", type="boolean", example=true),
     *                 @OA\Property(property="remarks", type="string", example="ممتاز"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نتيجة الامتحان غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نتيجة الامتحان غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $examResult = ExamResult::find($id);

        if (!$examResult) {
            return $this->error('نتيجة الامتحان غير موجودة', 404);
        }

        return $this->successResponse(
            new ExamResultResource($examResult),
            'تم جلب بيانات نتيجة الامتحان بنجاح',
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/exam-results/{id}",
     *     summary="تحديث بيانات نتيجة امتحان",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="معرف نتيجة الامتحان",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="exam_id", type="integer", example=2),
     *             @OA\Property(property="student_id", type="integer", example=2),
     *             @OA\Property(property="obtained_marks", type="number", format="float", example=90.00),
     *             @OA\Property(property="is_passed", type="boolean", example=true),
     *             @OA\Property(property="remarks", type="string", example="ممتاز جدًا")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم تحديث بيانات نتيجة الامتحان بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم تحديث بيانات نتيجة الامتحان بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="exam_id", type="integer", example=2),
     *                 @OA\Property(property="student_id", type="integer", example=2),
     *                 @OA\Property(property="obtained_marks", type="number", format="float", example=90.00),
     *                 @OA\Property(property="is_passed", type="boolean", example=true),
     *                 @OA\Property(property="remarks", type="string", example="ممتاز جدًا"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-02-01T00:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="نتيجة الامتحان غير موجودة",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="نتيجة الامتحان غير موجودة"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function update(UpdateExamResultRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $examResult = ExamResult::find($id);

            if (!$examResult) {
                DB::rollBack();
                return $this->error('نتيجة الامتحان غير موجودة', 404);
            }

            $user = Auth::user();

            $validatedData = $request->validated();

            /** @var \Modules\Users\Models\User|\Spatie\Permission\Traits\HasRoles $user */
            if ($user->hasRole('admin')) {
                $examResult->update($validatedData);

                DB::commit();

                return $this->successResponse(
                    new ExamResultResource($examResult),
                    'تم تحديث بيانات نتيجة الامتحان بنجاح',
                    200
                );
            }

            // 🔹 في حال المستخدم ليس مديرًا → إرسال طلب تعديل
            $editRequest = ExamResultEditRequest::create([
                'exam_result_id' => $examResult->id,
                'requester_id' => $user->id,
                'original_data' => $examResult->toArray(),
                'proposed_changes' => $validatedData,
                'reason' => $request->reason ?? null,
                'status' => 'pending',
            ]);

            // إرسال إشعار لكل المدراء
            $admins = \Modules\Users\Models\User::role('admin')->get();

            $tokens = [];
            foreach ($admins as $admin) {
                $tokens = array_merge($tokens, $admin->fcmTokens->pluck('token')->toArray());
            }

            if (!empty($tokens)) {
                $title = 'طلب تعديل نتيجة امتحان';
                $body = "تم طلب تعديل نتيجة الامتحان رقم #{$examResult->id}. الرجاء المراجعة والموافقة أو الرفض.";

                app(\App\Services\FirebaseService::class)
                    ->sendToMultipleTokens($tokens, $title, $body, [
                        'edit_request_id' => $editRequest->id,
                        'exam_result_id' => $examResult->id,
                        'action' => 'exam_result_edit_request'
                    ]);
            }

            DB::commit();

            return $this->successResponse(
                new ExamResultEditRequestResource($editRequest), // افترض أن لديك Resource لهذا النموذج
                'تم إرسال طلب التعديل وينتظر موافقة المدير',
                200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('حدث خطأ أثناء تحديث نتيجة الامتحان', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/exam-results/{id}",
     *     summary="Delete an exam result or request deletion",
     *     description="
     * Deletes an exam result if the authenticated user is an admin.  
     * If the user is not an admin, creates a deletion request and notifies admins via FCM.
     * 
     * **Behavior:**
     * - Admin: Direct deletion
     * - Non-admin: Creates `ExamResultEditRequest` of type `delete` and status `pending`
     * - Sends push notifications to all admins
     * ",
     *     tags={"Exam Results"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the exam result to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Reason for deletion (optional, defaults to 'طلب حذف نتيجة الامتحان')",
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="تم طلب حذف نتيجة بالخطأ")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     description="Admin deleted successfully",
     *                     @OA\Property(property="data", type="null"),
     *                     @OA\Property(property="message", type="string", example="تم حذف نتيجة الامتحان بنجاح"),
     *                     @OA\Property(property="status", type="integer", example=200)
     *                 ),
     *                 @OA\Schema(
     *                     description="Deletion request created for non-admin",
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="exam_result_id", type="integer", example=5),
     *                         @OA\Property(property="requester_id", type="integer", example=2),
     *                         @OA\Property(property="original_data", type="object"),
     *                         @OA\Property(property="proposed_changes", type="array", @OA\Items(type="string")),
     *                         @OA\Property(property="reason", type="string", example="طلب حذف نتيجة الامتحان"),
     *                         @OA\Property(property="status", type="string", example="pending"),
     *                         @OA\Property(property="type", type="string", example="delete"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     ),
     *                     @OA\Property(property="message", type="string", example="تم إرسال طلب حذف نتيجة الامتحان وينتظر موافقة المدير"),
     *                     @OA\Property(property="status", type="integer", example=200)
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Exam result not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="نتيجة الامتحان غير موجودة"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء طلب الحذف"),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $examResult = ExamResult::find($id);

            if (!$examResult) {
                return $this->error('نتيجة الامتحان غير موجودة', 404);
            }

            $user = Auth::user();

            /** @var \Modules\Users\Models\User|\Spatie\Permission\Traits\HasRoles $user */
            if ($user->hasRole('admin')) {
                // الإدارة تحذف مباشرة
                $examResult->delete();

                DB::commit();

                return $this->successResponse(
                    null,
                    'تم حذف نتيجة الامتحان بنجاح',
                    200
                );
            }

            // غير الإدارة → إنشاء طلب حذف
            $editRequest = ExamResultEditRequest::create([
                'exam_result_id' => $examResult->id,
                'requester_id' => $user->id,
                'original_data' => $examResult->toArray(),
                'proposed_changes' => [], // فارغ لأنو حذف
                'reason' => request()->input('reason') ?? 'طلب حذف نتيجة الامتحان',
                'status' => 'pending',
                'type' => 'delete', // النوع الجديد
            ]);

            // إرسال إشعار للمدراء
            $admins = \Modules\Users\Models\User::role('admin')->get();
            $tokens = [];
            foreach ($admins as $admin) {
                $tokens = array_merge($tokens, $admin->fcmTokens->pluck('token')->toArray());
            }

            if (!empty($tokens)) {
                $title = 'طلب حذف نتيجة امتحان';
                $body = "تم طلب حذف نتيجة الامتحان رقم #{$examResult->id}. الرجاء المراجعة.";

                app(\App\Services\FirebaseService::class)->sendToMultipleTokens($tokens, $title, $body, [
                    'edit_request_id' => $editRequest->id,
                    'exam_result_id' => $examResult->id,
                    'action' => 'exam_result_delete_request'
                ]);
            }

            DB::commit();

            return $this->successResponse(
                new ExamResultEditRequestResource($editRequest),
                'تم إرسال طلب حذف نتيجة الامتحان وينتظر موافقة المدير',
                200
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('حدث خطأ أثناء طلب الحذف', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/{exam_result_id}/edit-requests",
     *     summary="جلب جميع طلبات تعديل نتيجة امتحان معينة",
     *     description="يعيد قائمة بجميع طلبات التعديل المرتبطة بنتيجة امتحان محددة مرتبة تنازلياً حسب تاريخ الإنشاء",
     *     operationId="getEditRequestsByExamResult",
     *     tags={"Exam Result Edit Requests"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="exam_result_id",
     *         in="path",
     *         required=true,
     *         description="معرف نتيجة الامتحان",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب طلبات التعديل بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب طلبات تعديل نتيجة الامتحان بنجاح"),
     *         )
     *     ),
     *     @OA\Response(response=404, description="نتيجة الامتحان غير موجودة"),
     *     @OA\Response(response=500, description="خطأ داخلي في الخادم")
     * )
     */
    public function getEditRequestsByExamResult($exam_result_id)
    {
        try {
            $examResult = ExamResult::find($exam_result_id);

            if (!$examResult) {
                return $this->error('نتيجة الامتحان غير موجودة', 404);
            }

            // جلب كل طلبات التعديل المرتبطة بنتيجة الامتحان
            $editRequests = ExamResultEditRequest::where('exam_result_id', $examResult->id)
                ->with('examResult') // تجيب نتيجة الامتحان المرتبطة
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse(
                $editRequests,
                'تم جلب طلبات تعديل نتيجة الامتحان بنجاح',
                200
            );
        } catch (\Exception $e) {
            return $this->error('حدث خطأ أثناء جلب طلبات تعديل نتيجة الامتحان', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/exam-results/edit-requests/{id}/approve",
     *     summary="Approve an exam result edit or delete request",
     *     description="
     * Approves a pending `ExamResultEditRequest`.  
     * 
     * **Behavior:**
     * - If the request type is `delete`: deletes the exam result.
     * - If the request type is `edit`: applies the proposed changes to the exam result.
     * - Sends FCM notifications to:
     *   1. The requester
     *   2. The student
     *   3. The student's family
     * - Deletes the edit request after approval.
     * ",
     *     tags={"Exam Result Edit Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the exam result edit request to approve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", 
     *                 example="تمت الموافقة على طلب التعديل وتطبيق التغييرات بنجاح"
     *             ),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Edit request already processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="طلب التعديل تمت معالجته مسبقاً"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Edit request or related exam result not found",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="طلب التعديل غير موجود"),
     *                     @OA\Property(property="status", type="integer", example=404)
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="نتيجة الامتحان المرتبطة غير موجودة"),
     *                     @OA\Property(property="status", type="integer", example=404)
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء الموافقة على الطلب: ..."),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function approveEditRequest($id)
    {
        DB::beginTransaction();
        try {
            $editRequest = ExamResultEditRequest::with(
                'examResult',
                'examResult.student.user.fcmTokens',
                'examResult.student.family.user.fcmTokens',
                'requester.fcmTokens'
            )->find($id);

            if (!$editRequest) {
                return $this->error('طلب التعديل غير موجود', 404);
            }

            if ($editRequest->status !== 'pending') {
                return $this->error('طلب التعديل تمت معالجته مسبقاً', 400);
            }

            $examResult = $editRequest->examResult;

            if (!$examResult) {
                return $this->error('نتيجة الامتحان المرتبطة غير موجودة', 404);
            }

            $firebase = app(\App\Services\FirebaseService::class);

            if ($editRequest->type === 'delete') {
                // حالة الحذف
                $examResult->delete();

                $title = 'تمت الموافقة على حذف نتيجة الامتحان';
                $body  = "تم حذف نتيجة الامتحان رقم #{$examResult->id} بنجاح.";

            } else {
                // حالة التعديل (الافتراضي)
                $proposedChanges = $editRequest->proposed_changes;
                $examResult->update($proposedChanges);

                $title = 'تمت الموافقة على طلب تعديل نتيجة الامتحان';
                $body  = "تم تطبيق التغييرات على نتيجة الامتحان رقم #{$examResult->id}.";
            }

            // حذف الطلب بعد التنفيذ
            $editRequest->delete();
            DB::commit();

            // إرسال الإشعارات (نفس المتلقين في كلتا الحالتين)
            $data = [
                'exam_result_id' => $examResult->id,
                'action' => $editRequest->type === 'delete' ? 'exam_result_deleted' : 'edit_request_approved'
            ];

            // 1. مقدم الطلب
            $requester = $editRequest->requester;
            if ($requester && $requester->fcmTokens->isNotEmpty()) {
                $tokens = $requester->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            // 2. الطالب
            $studentUser = $examResult->student->user ?? null;
            if ($studentUser && $studentUser->fcmTokens->isNotEmpty()) {
                $tokens = $studentUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            // 3. ولي الأمر
            $familyUser = $examResult->student->family->user ?? null;
            if ($familyUser && $familyUser->fcmTokens->isNotEmpty()) {
                $tokens = $familyUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            return $this->successResponse(
                null,
                $editRequest->type === 'delete'
                    ? 'تم حذف نتيجة الامتحان بنجاح بعد الموافقة'
                    : 'تمت الموافقة على طلب التعديل وتطبيق التغييرات بنجاح',
                200
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('حدث خطأ أثناء الموافقة على الطلب: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/exam-results/edit-requests/{id}/reject",
     *     summary="Reject an exam result edit or delete request",
     *     description="
     * Rejects a pending `ExamResultEditRequest`.  
     * 
     * **Behavior:**
     * - Deletes the edit request after rejection.
     * - Sends FCM notifications to:
     *   1. The requester
     *   2. The student
     *   3. The student's family
     * - The notification title varies based on the request type (`delete` or `edit`).
     * ",
     *     tags={"Exam Result Edit Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the exam result edit request to reject",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success response",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="تم رفض الطلب وحذفه بنجاح"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Edit request already processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="طلب التعديل تمت معالجته مسبقاً"),
     *             @OA\Property(property="status", type="integer", example=400)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Edit request not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="طلب التعديل غير موجود"),
     *             @OA\Property(property="status", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="حدث خطأ أثناء رفض الطلب: ..."),
     *             @OA\Property(property="status", type="integer", example=500)
     *         )
     *     )
     * )
     */
    public function rejectEditRequest($id)
    {
        DB::beginTransaction();
        try {
            $editRequest = ExamResultEditRequest::with(
                'examResult',
                'examResult.student.user.fcmTokens',
                'examResult.student.family.user.fcmTokens',
                'requester.fcmTokens'
            )->find($id);

            if (!$editRequest) {
                return $this->error('طلب التعديل غير موجود', 404);
            }

            if ($editRequest->status !== 'pending') {
                return $this->error('طلب التعديل تمت معالجته مسبقاً', 400);
            }

            $examResultId = $editRequest->exam_result_id ?? ($editRequest->examResult->id ?? null);

            // تحديد نوع الرفض
            $title = $editRequest->type === 'delete'
                ? 'تم رفض طلب حذف نتيجة الامتحان'
                : 'تم رفض طلب تعديل نتيجة الامتحان';

            $body = "$title رقم #{$examResultId}.";

            $editRequest->delete();
            DB::commit();

            $firebase = app(\App\Services\FirebaseService::class);

            $data = [
                'exam_result_id' => $examResultId,
                'action' => 'edit_request_rejected'
            ];

            // إشعارات نفس المتلقين
            $requester = $editRequest->requester;
            if ($requester && $requester->fcmTokens->isNotEmpty()) {
                $tokens = $requester->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            $studentUser = $editRequest->examResult?->student?->user;
            if ($studentUser && $studentUser->fcmTokens->isNotEmpty()) {
                $tokens = $studentUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            $familyUser = $editRequest->examResult?->student?->family?->user;
            if ($familyUser && $familyUser->fcmTokens->isNotEmpty()) {
                $tokens = $familyUser->fcmTokens->pluck('token')->toArray();
                if (!empty($tokens)) {
                    $firebase->sendToMultipleTokens($tokens, $title, $body, $data);
                }
            }

            return $this->successResponse(null, 'تم رفض الطلب وحذفه بنجاح', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('حدث خطأ أثناء رفض الطلب: ' . $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/filter",
     *     summary="جلب نتائج الامتحانات لطالب معيّن مع فلاتر اختيارية",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=true,
     *         description="معرّف الطالب",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         description="تاريخ الامتحان (يوم محدد)",
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         required=false,
     *         description="تاريخ بداية الفترة",
     *         @OA\Schema(type="string", format="date", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         required=false,
     *         description="تاريخ نهاية الفترة",
     *         @OA\Schema(type="string", format="date", example="2025-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="subject_id",
     *         in="query",
     *         required=false,
     *         description="معرّف المادة",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="marks_from",
     *         in="query",
     *         required=false,
     *         description="الحد الأدنى للعلامة",
     *         @OA\Schema(type="number", format="float", example=50)
     *     ),
     *     @OA\Parameter(
     *         name="marks_to",
     *         in="query",
     *         required=false,
     *         description="الحد الأعلى للعلامة",
     *         @OA\Schema(type="number", format="float", example=100)
     *     ),
     *     @OA\Parameter(
     *         name="is_passed",
     *         in="query",
     *         required=false,
     *         description="النجاح في الامتحان (1 ناجح / 0 راسب)",
     *         @OA\Schema(type="integer", enum={0,1}, example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب نتائج الامتحانات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب نتائج الامتحانات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ExamResultResource")
     *             )
     *         )
     *     )
     * )
     */
    public function filter(FilterExamResultsRequest $request)
    {
        $filters = $request->validated();

        $results = $this->examResultFilterService->filter($filters);    

        return $this->successResponse(
            ExamResultResource::collection($results),
            'تم جلب نتائج الامتحانات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/student-exam-results",
     *     summary="Get student exam results based on student or batch",
     *     description="يُرجع نتائج الامتحانات بناءً على الفلاتر المقدمة. إذا تم توفير student_id، ستُرجع جميع نتائج الامتحانات الخاصة بهذا الطالب. إذا تم توفير batch_id فقط (دون student_id)، ستُرجع أحدث نتيجة امتحان لكل طالب في الدفعة المحددة. أما إذا لم يتم توفير أي فلاتر، فستُرجع أحدث نتيجة امتحان لكل طالب في النظام.",
     *     tags={"ExamResults"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="student_id",
     *         in="query",
     *         required=false,
     *         description="معرف الطالب. إذا تم توفيره، ستُرجع جميع نتائج الامتحانات الخاصة بهذا الطالب.",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=false,
     *         description="معرف الدفعة. إذا تم توفيره بدون student_id، ستُرجع أحدث نتيجة امتحان لكل طالب في هذه الدفعة فقط.",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Exam results retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="count", type="integer", example=2),
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=123),
     *                         @OA\Property(property="exam_id", type="integer", example=45),
     *                         @OA\Property(property="student_first_name", type="string", example="Ahmad"),
     *                         @OA\Property(property="student_last_name", type="string", example="Alhamwi"),
     *                         @OA\Property(property="obtained_marks", type="number", example=78),
     *                         @OA\Property(property="is_passed", type="string", example="Passed"),
     *                         @OA\Property(property="subject_name", type="string", example="Mathematics"),
     *                         @OA\Property(property="exam_date", type="string", format="date", example="2025-03-15"),
     *                         @OA\Property(property="exam_type", type="string", example="Final"),
     *                         @OA\Property(property="exam_time", type="string", example="10:00"),
     *                         @OA\Property(property="total_marks", type="integer", example=100),
     *                         @OA\Property(property="passing_marks", type="integer", example=60)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected server error"
     *     )
     * )
     */
    public function getStudentExamResults(Request $request)
    {
        $studentId = $request->query('student_id'); // اختياري
        $batchId   = $request->query('batch_id');   // اختياري

        $results = collect();

        /*
        |--------------------------------------------------------------------------
        | الحالة 1: تم اختيار طالب → نعرض جميع علاماته
        |--------------------------------------------------------------------------
        */
        if ($studentId) {

            $examResultsQuery = ExamResult::query()
                ->with([
                    'exam.batchSubject.batch',
                    'exam.batchSubject.subject',
                    'exam.examType',
                    'student',
                ])
                ->where('student_id', $studentId)
                ->orderBy('created_at', 'desc');

            if ($batchId) {
                $examResultsQuery->whereHas('exam.batchSubject', function ($q) use ($batchId) {
                    $q->where('batch_id', $batchId);
                });
            }

            $examResults = $examResultsQuery->get();

            foreach ($examResults as $examResult) {
                $exam    = $examResult->exam;
                $student = $examResult->student;

                if (!$exam || !$student) {
                    continue;
                }

                $results->push([
                    'id'                 => $examResult->id,
                    'exam_id'            => $exam->id,
                    'student_first_name' => $student->first_name ?? 'غير متوفر',
                    'student_last_name'  => $student->last_name ?? 'غير متوفر',

                    'obtained_marks'     => $examResult->obtained_marks,
                    'is_passed'          => $examResult->is_passed ? 'ناجح' : 'راسب',

                    'attendance_status'  => 'حاضر',

                    'subject_name'       => $exam->batchSubject?->subject?->name ?? 'غير معروف',

                    'exam_date'          => $exam->exam_date?->format('Y-m-d'),
                    'exam_type'          => $exam->examType?->name ?? 'غير محدد',
                    'exam_time'          => $exam->exam_time,

                    'total_marks'        => $exam->total_marks,
                    'passing_marks'      => $exam->passing_marks,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | الحالة 2 و 3:
        | - لا يوجد طالب
        | - نعرض آخر علامة فقط لكل طالب
        | - مع أو بدون فلترة شعبة
        |--------------------------------------------------------------------------
        */
        else {

            $examResultsQuery = ExamResult::query()
                ->with([
                    'exam.batchSubject.batch',
                    'exam.batchSubject.subject',
                    'exam.examType',
                    'student',
                ])
                ->orderBy('created_at', 'desc');

            if ($batchId) {
                $examResultsQuery->whereHas('exam.batchSubject', function ($q) use ($batchId) {
                    $q->where('batch_id', $batchId);
                });
            }

            // نجلب كل النتائج ثم نأخذ آخر نتيجة لكل طالب
            $examResults = $examResultsQuery->get()
                ->groupBy('student_id')
                ->map(function ($studentResults) {
                    return $studentResults->sortByDesc('created_at')->first();
                })
                ->values();

            foreach ($examResults as $examResult) {
                $exam    = $examResult->exam;
                $student = $examResult->student;

                if (!$exam || !$student) {
                    continue;
                }

                $results->push([
                    'id'                 => $examResult->id,
                    'exam_id'            => $exam->id,
                    'student_first_name' => $student->first_name ?? 'غير متوفر',
                    'student_last_name'  => $student->last_name ?? 'غير متوفر',

                    'obtained_marks'     => $examResult->obtained_marks,
                    'is_passed'          => $examResult->is_passed ? 'ناجح' : 'راسب',

                    'attendance_status'  => 'حاضر',

                    'subject_name'       => $exam->batchSubject?->subject?->name ?? 'غير معروف',

                    'exam_date'          => $exam->exam_date?->format('Y-m-d'),
                    'exam_type'          => $exam->examType?->name ?? 'غير محدد',
                    'exam_time'          => $exam->exam_time,

                    'total_marks'        => $exam->total_marks,
                    'passing_marks'      => $exam->passing_marks,
                ]);
            }
        }

        return $this->successResponse(
            [
                'count' => $results->count(),
                'items' => $results->values(),
            ],
            'تم جلب البيانات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/exam-results/exam/{examId}",
     *     operationId="getExamResults",
     *     tags={"ExamResults"},
     *     summary="جلب نتائج امتحان معين",
     *     description="يرجع جميع نتائج الامتحان مع معلومات الطالب.",
     *     security={{"sanctum": {}}},   
     *     @OA\Parameter(
     *         name="examId",
     *         in="path",
     *         description="معرّف الامتحان",
     *         required=true,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب البيانات بنجاح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="exam", type="string", example="Math 101"),
     *             @OA\Property(property="count", type="integer", example=2),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="exam_id", type="integer", example=10),
     *                     @OA\Property(property="student_id", type="integer", example=55),
     *                     @OA\Property(property="student_name", type="string", example="Ali Mohamed"),
     *                     @OA\Property(property="obtained_marks", type="number", format="float", example=87.5),
     *                     @OA\Property(property="is_passed", type="boolean", example=true),
     *                     @OA\Property(property="remarks", type="string", example="أداء ممتاز"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-01-24T10:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-01-24T10:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الامتحان غير موجود",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الامتحان غير موجود")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="غير مصرح - توكن Sanctum غير موجود أو غير صالح",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function getExamResults($examId)
    {
        // تحقق من وجود الامتحان
        $exam = Exam::find($examId);
        if (!$exam) {
            return $this->error('الامتحان غير موجود', 404);
        }

        // جلب نتائج الامتحان مع الطالب
        $results = ExamResult::with('student')
            ->where('exam_id', $examId)
            ->get();
    
        return $this->successResponse(
            [
                'exam' => $exam->name, // اسم الامتحان
                'count' => $results->count(),
                'items' => ExamResultDetailResource::collection($results), 
            ],
            'تم جلب البيانات بنجاح',
            200
        );
    }
    
    /**
     * @OA\Get(
     *     path="/api/exam-results/edit-requests",
     *     operationId="getAllExamResultEditRequests",
     *     tags={"Exam Result Edit Requests"},
     *     summary="Get all exam result edit requests",
     *     description="Retrieve all exam result edit requests including student and exam information",
     *     security={{"sanctum": {}}},   
     *
     *     @OA\Response(
     *         response=200,
     *         description="All edit requests retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب جميع طلبات تعديل النتائج بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="exam_result_id", type="integer", example=5),
     *                     @OA\Property(property="requested_mark", type="number", format="float", example=85),
     *                     @OA\Property(property="reason", type="string", example="خطأ في جمع الدرجات"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *
     *                     @OA\Property(
     *                         property="exam_result",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="mark", type="number", format="float", example=75),
     *
     *                         @OA\Property(
     *                             property="student",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=10),
     *                             @OA\Property(property="full_name", type="string", example="Ahmad Mohammad")
     *                         ),
     *
     *                         @OA\Property(
     *                             property="exam",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=3),
     *                             @OA\Property(property="name", type="string", example="Midterm Exam")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function getAllEditRequests(\Illuminate\Http\Request $request)
    {
        try {
            $status = $request->query('status');

            $query = ExamResultEditRequest::with([
                'examResult',
                'examResult.student',
                'examResult.exam',
            ]);

            if ($status) {
                $query->where('status', $status);
            }

            $editRequests = $query->orderBy('created_at', 'desc')->get();

            return $this->successResponse(
                $editRequests,
                'تم جلب جميع طلبات تعديل النتائج بنجاح',
                200
            );

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Exam Edit Requests Error: " . $e->getMessage());
            return $this->error('حدث خطأ أثناء جلب طلبات التعديل: ' . $e->getMessage(), 500);
        }
    }
}
