<?php

namespace Modules\BatchStudents\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\BatchStudents\Http\Requests\BulkAssignStudentsToBatchRequest;
use Modules\BatchStudents\Http\Resources\UnassignedStudentResource;
use Modules\BatchStudents\Services\BulkBatchAssignmentService;
use Modules\Shared\Traits\SuccessResponseTrait;

class BulkBatchAssignmentController extends Controller
{
    use SuccessResponseTrait;

    protected BulkBatchAssignmentService $service;

    public function __construct(BulkBatchAssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/batch-students/unassigned",
     *     summary="جلب الطلاب غير المرتبطين بأي شعبة، مع فلترة حسب الشعبة المستهدفة",
     *     description="
     * يجلب جميع الطلاب الذين ليس لديهم أي ارتباط بشعبة، مع فلترة ذكية:
     * - **الفرع الأكاديمي**: طلاب نفس الفرع الأكاديمي للشعبة + طلاب بدون فرع
     * - **الموقع الجغرافي**: حسب قيمة location_filter
     *
     * 🔹 قيم location_filter:
     * - `same_location` (افتراضي): فقط طلاب نفس الموقع الجغرافي للشعبة
     * - `no_location`: فقط طلاب بدون موقع جغرافي محدد
     * - `all`: جميع الطلاب بغض النظر عن الموقع
     *
     * 🔹 حقل assignment_status لكل طالب:
     * - `matching`: يطابق الفرع والموقع
     * - `no_location`: بدون موقع جغرافي
     * - `no_branch`: بدون فرع أكاديمي
     * - `no_branch_no_location`: بدون فرع ولا موقع
     * ",
     *     tags={"Batch Students - Bulk Assignment"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batch_id",
     *         in="query",
     *         required=true,
     *         description="معرف الشعبة المستهدفة",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="location_filter",
     *         in="query",
     *         required=false,
     *         description="نوع الفلترة الجغرافية: same_location | no_location | all",
     *         @OA\Schema(type="string", enum={"same_location","no_location","all"}, example="all")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الطلاب غير المرتبطين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الطلاب غير المرتبطين بأي شعبة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="first_name", type="string", example="محمد"),
     *                     @OA\Property(property="last_name", type="string", example="أحمد"),
     *                     @OA\Property(property="full_name", type="string", example="محمد أحمد"),
     *                     @OA\Property(property="gender", type="string", example="male"),
     *                     @OA\Property(property="assignment_status", type="string", example="matching"),
     *                     @OA\Property(property="assignment_status_description", type="string", example="يطابق الفرع الأكاديمي والموقع الجغرافي للشعبة")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total_count", type="integer", example=15),
     *                 @OA\Property(property="batch_name", type="string", example="بكالوريا ذكور"),
     *                 @OA\Property(property="location_filter", type="string", example="all")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="batch_id مطلوب أو غير صالح"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="الشعبة غير موجودة"
     *     )
     * )
     */
    public function unassignedStudents(Request $request)
    {
        $request->validate([
            'batch_id'        => 'required|integer|exists:batches,id',
            'location_filter' => 'nullable|string|in:same_location,no_location,all',
        ], [
            'batch_id.required' => 'معرف الشعبة مطلوب',
            'batch_id.integer'  => 'معرف الشعبة يجب أن يكون عدد صحيح',
            'batch_id.exists'   => 'الشعبة غير موجودة',
            'location_filter.in' => 'قيمة الفلتر الجغرافي غير صالحة. القيم المتاحة: same_location, no_location, all',
        ]);

        $batchId        = (int) $request->input('batch_id');
        $locationFilter = $request->input('location_filter', 'same_location');

        $result   = $this->service->getUnassignedStudents($batchId, $locationFilter);
        $batch    = $result['batch'];
        $students = $result['students'];

        return $this->successResponse(
            UnassignedStudentResource::collection($students),
            'تم جلب الطلاب غير المرتبطين بأي شعبة بنجاح',
            200,
            [
                'total_count'     => $students->count(),
                'batch_id'        => $batch->id,
                'batch_name'      => $batch->name,
                'academic_branch' => $batch->academicBranch?->name,
                'institute_branch' => $batch->instituteBranch?->name,
                'location_filter' => $locationFilter,
            ]
        );
    }

    /**
     * @OA\Post(
     *     path="/api/batch-students/bulk-assign",
     *     summary="إضافة مجموعة طلاب إلى شعبة معينة",
     *     description="
     * يقوم بإضافة مجموعة من الطلاب غير المرتبطين بأي شعبة إلى شعبة محددة.
     *
     * 🔹 **سلوك تلقائي:**
     * - الطلاب الذين ليس لديهم موقع جغرافي (`institute_branch_id = null`) يتم ربطهم تلقائياً بموقع الشعبة.
     *
     * 🔹 **شروط:**
     * - جميع الطلاب يجب أن يكونوا غير مرتبطين بأي شعبة.
     * - إذا كان أي طالب مرتبط بشعبة بالفعل، ستفشل العملية مع قائمة بالطلاب المرتبطين.
     * ",
     *     tags={"Batch Students - Bulk Assignment"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"batch_id","student_ids"},
     *             @OA\Property(property="batch_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="student_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={5, 8, 12, 15}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تمت الإضافة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تمت إضافة 4 طلاب إلى الشعبة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_assigned", type="integer", example=4),
     *                 @OA\Property(property="location_updated_count", type="integer", example=2),
     *                 @OA\Property(property="batch_id", type="integer", example=1),
     *                 @OA\Property(property="batch_name", type="string", example="بكالوريا ذكور")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=409,
     *         description="بعض الطلاب مرتبطون بشعبة بالفعل",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="بعض الطلاب مرتبطون بشعبة بالفعل"),
     *             @OA\Property(
     *                 property="already_assigned_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={5, 12}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=422, description="خطأ في التحقق من المدخلات")
     * )
     */
    public function bulkAssign(BulkAssignStudentsToBatchRequest $request)
    {
        $result = $this->service->bulkAssign(
            $request->validated()['batch_id'],
            $request->validated()['student_ids']
        );

        if (!$result['success']) {
            return $this->error(
                $result['message'],
                409,
                ['already_assigned_ids' => $result['already_assigned_ids']]
            );
        }

        return $this->successResponse(
            $result,
            "تمت إضافة {$result['total_assigned']} طلاب إلى الشعبة بنجاح",
            200
        );
    }
}
