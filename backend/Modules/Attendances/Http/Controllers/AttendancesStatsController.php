<?php

namespace Modules\Attendances\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Attendances\Services\AttendanceStatisticsService as ServicesAttendanceStatisticsService;

use Modules\Shared\Traits\SuccessResponseTrait;

class AttendancesStatsController extends Controller
{
    use SuccessResponseTrait;

/**
 * @OA\Get(
 *     path="/api/attendance/stats/summary",
 *     summary="إحصائيات الحضور والغياب",
 *     description="يعيد نسب الحضور والغياب مع دعم الفلترة حسب الفرع، الشعب، والفترة الزمنية (أسبوع ماضي، شهر ماضي أو كامل المدة).",
 *     tags={"Attendance"},
 *     security={{"sanctum":{}}},
 *
 *     @OA\Parameter(
 *         name="institute_branch_id",
 *         in="query",
 *         required=false,
 *         description="فلترة حسب الموقع الجغرافي (فرع المعهد)",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Parameter(
 *         name="batch_ids[]",
 *         in="query",
 *         required=false,
 *         description="فلترة حسب الشعب (يمكن تمرير أكثر من شعبة)",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(type="integer", example=73)
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="period",
 *         in="query",
 *         required=false,
 *         description="الفترة الزمنية للإحصائيات",
 *         @OA\Schema(
 *             type="string",
 *             enum={"last_week","last_month"},
 *             example="last_week"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب إحصائيات الحضور والغياب بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="تم جلب إحصائيات الحضور والغياب بنجاح"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="attendance_percentage", type="number", format="float", example=82.35),
 *                 @OA\Property(property="absence_percentage", type="number", format="float", example=17.65),
 *                 @OA\Property(property="present_count", type="integer", example=140),
 *                 @OA\Property(property="absent_count", type="integer", example=30),
 *                 @OA\Property(property="total_records", type="integer", example=170)
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="لا توجد بيانات حضور ضمن الفلاتر المحددة",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="لا توجد بيانات حضور ضمن الفلاتر المحددة"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function index(Request $request, ServicesAttendanceStatisticsService $service)
    {
        $data = $service->calculate([
            'institute_branch_id' => $request->input('institute_branch_id'),
            'batch_ids'           => $request->input('batch_ids'),
            'period'              => $request->input('period'),
        ]);

        return $this->successResponse(
            $data,
            'تم جلب إحصائيات الحضور والغياب بنجاح'
        );
    }
}
