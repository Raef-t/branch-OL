<?php

namespace Modules\Batches\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Batches\Models\Batch;
use Modules\Batches\Services\BatchPerformanceService;
use Modules\Shared\Traits\SuccessResponseTrait;

/**
 * @OA\Tag(
 *     name="Batch Performance",
 *     description="مؤشرات الأداء الأكاديمي للدورات"
 * )
 */
class BatchPerformanceController extends Controller
{
    use SuccessResponseTrait;

    protected BatchPerformanceService $performanceService;

    public function __construct(BatchPerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * @OA\Get(
     *     path="/api/batches/performance/all",
     *     operationId="getAllBatchPerformance",
     *     summary="جلب نسب الأداء لجميع الدورات",
     *     tags={"Batch Performance"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب نسب أداء الدورات بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب نسب أداء الدورات بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="batch_id", type="integer", example=84),
     *                     @OA\Property(property="batch_name", type="string", example="بكالوريا علمي مختلط صيف 2024"),
     *                     @OA\Property(property="percentage", type="number", format="float", example=78.45, nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = $this->performanceService->getAllBatchesWithPerformance();

        return $this->successResponse(
            $data,
            'تم جلب نسب أداء الدورات بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/batches/performance/{batchId}",
     *     operationId="getBatchPerformance",
     *     summary="جلب نسبة الأداء لدورة معينة",
     *     tags={"Batch Performance"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(
     *         name="batchId",
     *         in="path",
     *         required=true,
     *         description="معرف الدورة",
     *         @OA\Schema(type="integer", example=84)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب نسبة أداء الدورة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب نسبة أداء الدورة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="batch_id", type="integer", example=84),
     *                 @OA\Property(property="batch_name", type="string", example="بكالوريا علمي مختلط صيف 2024"),
     *                 @OA\Property(property="percentage", type="number", format="float", example=82.30)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="الدورة غير موجودة أو لا تملك نتائج",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="الدورة غير موجودة أو لا توجد نتائج لهذه الدورة"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function show(int $batchId)
    {
        $batch = Batch::find($batchId);

        if (!$batch) {
            return $this->error('الدورة غير موجودة', 404);
        }

        $percentage = $this->performanceService->calculateBatchPercentage($batchId);

        if ($percentage === null) {
            return $this->error('لا توجد نتائج لهذه الدورة', 404);
        }

        return $this->successResponse(
            [
                'batch_id'   => $batch->id,
                'batch_name' => $batch->name,
                'percentage' => $percentage,
            ],
            'تم جلب نسبة أداء الدورة بنجاح',
            200
        );
    }

    /**
     * @OA\Get(
     *     path="/api/batches/performance/top",
     *     operationId="getTopBatchPerformance",
     *     summary="جلب الدورة المتفوقة",
     *     tags={"Batch Performance"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="تم جلب الدورة المتفوقة بنجاح",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="تم جلب الدورة المتفوقة بنجاح"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="batch_id", type="integer", example=84),
     *                 @OA\Property(property="batch_name", type="string", example="بكالوريا علمي مختلط صيف 2024"),
     *                 @OA\Property(property="percentage", type="number", format="float", example=91.75)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="لا توجد دورات تملك نتائج",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="لا توجد دورات تملك نتائج"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function top()
    {
        $topBatch = $this->performanceService->getTopBatch();

        if (!$topBatch) {
            return $this->error('لا توجد دورات تملك نتائج', 404);
        }

        return $this->successResponse(
            $topBatch,
            'تم جلب الدورة المتفوقة بنجاح',
            200
        );
    }
}
