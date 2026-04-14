<?php

namespace Modules\Batches\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Batches\Models\Batch;
use Modules\BatchStudents\Models\BatchStudent;

class BatchPerformanceService
{
    /**
     * نخزن هنا نسب الدورات بعد حسابها لمرة واحدة في كل request
     * key = batch_id, value = float|null
     *
     * @var \Illuminate\Support\Collection|null
     */
    protected ?Collection $batchPercentages = null;

    /**
     * يبني خريطة (batch_id => percentage|null) باستعلام واحد فقط
     */
    protected function buildBatchPercentages(): Collection
    {
        $batchStudentsTable = (new BatchStudent())->getTable();

        // 📌 استعلام واحد يجمع مجموع العلامات لكل طالب في كل دورة
        $rows = DB::table('exam_results as er')
            ->join('exams as e', 'e.id', '=', 'er.exam_id')
            ->join($batchStudentsTable . ' as bs', 'bs.student_id', '=', 'er.student_id')
            ->selectRaw('
                bs.batch_id,
                er.student_id,
                SUM(er.obtained_marks) as total_obtained,
                SUM(e.total_marks)     as total_max
            ')
            ->groupBy('bs.batch_id', 'er.student_id')
            ->get();

        if ($rows->isEmpty()) {
            // لا يوجد أي نتائج في النظام
            return collect();
        }

        // 🔹 نجمع حسب الدورة
        $byBatch = $rows->groupBy('batch_id');

        // 🔹 نحسب نسبة كل دورة = متوسط نسب الطلاب
        $percentages = $byBatch->map(function (Collection $studentRows, $batchId) {
            $studentPercentages = $studentRows->map(function ($row) {
                if ($row->total_max <= 0) {
                    return null;
                }

                return ($row->total_obtained / $row->total_max) * 100;
            })->filter(); // نحذف null

            if ($studentPercentages->isEmpty()) {
                return null;
            }

            return round($studentPercentages->avg(), 2);
        });

        return $percentages;
    }

    /**
     * يحصل على خريطة نسب الدورات مع cache داخل السيرفس
     */
    protected function getBatchPercentages(): Collection
    {
        if ($this->batchPercentages === null) {
            $this->batchPercentages = $this->buildBatchPercentages();
        }

        return $this->batchPercentages;
    }

    /**
     * حساب نسبة دورة واحدة
     */
    public function calculateBatchPercentage(int $batchId): ?float
    {
        $percentages = $this->getBatchPercentages();

        // لو لم تُحسب هذه الدورة أصلاً، ترجع null
        return $percentages->get($batchId);
    }

    /**
     * جلب جميع الدورات مع نسبها
     */
    public function getAllBatchesWithPerformance(): Collection
    {
        $percentages = $this->getBatchPercentages();

        // Batch::all() يحترم الـ GlobalScope (VisibleBatchScope)
        return Batch::all()->map(function (Batch $batch) use ($percentages) {
            return [
                'batch_id'   => $batch->id,
                'batch_name' => $batch->name,
                'percentage' => $percentages->get($batch->id, null),
            ];
        });
    }

    /**
     * جلب الدورة المتفوقة
     */
    public function getTopBatch(): ?array
    {
        $all = $this->getAllBatchesWithPerformance()
            ->filter(fn ($b) => $b['percentage'] !== null)
            ->sortByDesc('percentage')
            ->values();

        return $all->first();
    }
}
