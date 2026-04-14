<?php

declare(strict_types=1);

namespace Modules\Students\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Modules\ClassSchedules\Models\ClassSchedule as ModelsClassSchedule;
use Modules\Exams\Models\Exam;
use Modules\Students\Domain\Schedule\Exceptions\ScheduleNotFoundException;
use Modules\Students\Models\Student as ModelsStudent;
use Modules\Students\Domain\Schedule\Enums\ScheduleSourceType;
use Modules\Students\Application\Schedule\Data\GetScheduleData;

final class ScheduleService
{
    public function getSchedule(GetScheduleData $data): array
    {
        $query = ModelsClassSchedule::query()
            ->where('is_active', true)
            ->with([
                'batchSubject.subject',
                'batchSubject.batch:id,name,institute_branch_id',
                'batchSubject.batch.batchEmployees.employee:id,first_name,last_name,photo_path',
                'classRoom',
                'batchSubject', // مطلوب لمقارنة batch_subject_id
            ]);

        $this->applyDayFilter($query, $data->day);

        // جلب الامتحانات ذات الصلة
        $relevantExams = $this->loadRelevantExams($data->day);

        // معالجة نوع الموقع الجغرافي
        if ($data->type === ScheduleSourceType::LOCATION) {
            if ($data->instituteBranchId === null) {
                throw new \InvalidArgumentException('Institute branch ID is required for location type');
            }

            $query->whereHas('batchSubject.batch', function (Builder $q) use ($data) {
                $q->where('institute_branch_id', $data->instituteBranchId);
            });
        } else {
            $batchId = $this->resolveBatchId($data);
            $query->whereHas('batchSubject.batch', function (Builder $q) use ($batchId, $data) {
                $q->where('id', $batchId);
                if ($data->instituteBranchId !== null) {
                    $q->where('institute_branch_id', $data->instituteBranchId);
                }
            });
        }

        if ($data->isDefault !== null) {
            $query->where('is_default', $data->isDefault);
        }

        $schedules = $query
            ->orderBy('day_of_week')
            ->orderBy('period_number')
            ->get();

        return [
            'schedules' => $schedules,
            'exams' => $relevantExams
        ];
    }

    private function applyDayFilter(Builder $query, string $day): void
    {
        if ($day === 'today') {
            $query->where('day_of_week', CarbonImmutable::now()->englishDayOfWeek);
            return;
        }

        if ($day !== 'all') {
            $query->where('day_of_week', $day);
        }
    }

    private function resolveBatchId(GetScheduleData $data): int
    {
        return match ($data->type) {
            ScheduleSourceType::STUDENT => $this->resolveStudentBatch($data->id),
            ScheduleSourceType::BATCH => $data->id,
            default => throw new \InvalidArgumentException("Unsupported schedule type: {$data->type->value}"),
        };
    }

    private function resolveStudentBatch(int $studentId): int
    {
        $student = ModelsStudent::query()
            ->with('latestBatchStudent')
            ->findOrFail($studentId);

        if ($student->latestBatchStudent === null) {
            throw new ScheduleNotFoundException('الطالب غير مرتبط بأي دفعة حالياً');
        }

        return $student->latestBatchStudent->batch_id;
    }

    private function loadRelevantExams(string $dayFilter): Collection
    {
        $examQuery = Exam::query()
            ->with('batchSubject');

        // ✅ الإصلاح: إزالة شرط is_active لأن العمود غير موجود في جدول الامتحانات
        // ملاحظة: هذا الحقل غير موجود في الهيكل الحالي لقاعدة البيانات
        // إذا أردنا تفعيل هذا الشرط لاحقاً، يجب إضافة العمود إلى الجدول أولاً

        if ($dayFilter === 'today') {
            $examQuery->whereDate('exam_date', now()->toDateString());
        } elseif ($dayFilter === 'all') {
            // نقوم بتحميل الامتحانات لـ 14 يوماً قادمة لتغطية معظم الحالات
            $startDate = now();
            $endDate = now()->addDays(14);
            $examQuery->whereBetween('exam_date', [$startDate, $endDate]);
        } else {
            // يوم محدد (مثل Sunday)
            $date = now()->next($dayFilter)->toDateString();
            $examQuery->whereDate('exam_date', $date);
        }

        return $examQuery->get();
    }
}
