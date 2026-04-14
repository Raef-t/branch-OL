<?php

namespace Modules\Exams\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Exams\Filters\ExamsStatisticsFilter;
use Modules\Exams\Services\ExamsStatisticsService;
use Modules\Shared\Traits\SuccessResponseTrait;

class ExamsStatisticsController extends Controller
{
    use SuccessResponseTrait;

    public function __construct(
        protected ExamsStatisticsService $statisticsService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/exams/statistics/top-performers",
     *     summary="جلب الطلاب المتفوقين (90% فأكثر) لشهر معين",
     *     description="يتم حساب المعدل لجميع امتحانات الطالب خلال الشهر والسنة المختارين، مع إمكانية الفلترة حسب نوع الامتحان وفرع المعهد الجغرافي.",
     *     tags={"Exams Statistics"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         required=false,
     *         description="الشهر (1-12)، الافتراضي الشهر الحالي",
     *         @OA\Schema(type="integer", minimum=1, maximum=12, example=2)
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         required=false,
     *         description="السنة، الافتراضي السنة الحالية",
     *         @OA\Schema(type="integer", example=2026)
     *     ),
     *     @OA\Parameter(
     *         name="exam_type_id",
     *         in="query",
     *         required=false,
     *         description="معرف نوع الامتحان (اختياري)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="institute_branch_id",
     *         in="query",
     *         required=false,
     *         description="معرف فرع المعهد الجغرافي (اختياري، للفلترة حسب الموقع الجغرافي)",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب قائمة المتفوقين بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب قائمة المتفوقين بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="student_id", type="integer", example=101),
     *                     @OA\Property(property="student_name", type="string", example="أحمد محمد"),
     *                     @OA\Property(property="institute_branch_name", type="string", example="فرع دمشق الرئيسي"),
     *                     @OA\Property(property="total_obtained", type="number", format="float", example=185.5),
     *                     @OA\Property(property="total_possible", type="integer", example=200),
     *                     @OA\Property(property="average_percentage", type="number", format="float", example=92.75)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="خطأ في مدخلات الفلترة",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getTopPerformers(Request $request)
    {
        $filter = ExamsStatisticsFilter::fromRequest($request);
        $topPerformers = $this->statisticsService->getMonthlyTopPerformers($filter);

        return $this->successResponse(
            $topPerformers,
            'تم جلب قائمة المتفوقين بنجاح'
        );
    }
}
