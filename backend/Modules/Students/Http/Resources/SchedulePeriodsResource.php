<?php

declare(strict_types=1);

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Modules\Students\Services\ClassScheduleTypeService;

final class SchedulePeriodsResource extends JsonResource
{
    private Collection $exams;

    // الإصلاح: استخدام ?Collection للسماح بالـ null بشكل صريح
    public function __construct($resource, ?Collection $exams = null)
    {
        parent::__construct($resource);
        $this->exams = $exams ?? collect();
    }

    public function toArray($request): array
    {
        /** @var Collection $schedules */
        $schedules = $this->resource['schedules'] ?? collect();

        if ($schedules->isEmpty()) {
            return [
                'periods_count' => 0,
                'periods' => (object) [],
            ];
        }

        $periods = [];
        $processedPeriods = [];
        $typeService = new ClassScheduleTypeService($this->exams);

        foreach ($schedules as $schedule) {
            $dayKey = $schedule->day_of_week;
            $periodKey = 'الحصة ' . $schedule->period_number;
            $uniqueKey = "{$dayKey}-{$periodKey}-{$schedule->batchSubject->batch_id}-{$schedule->batchSubject->subject_id}-{$schedule->id}";

            // تجنب التكرار في نفس الحصة لنفس الدفعة والمادة
            if (isset($processedPeriods[$uniqueKey])) {
                continue;
            }

            $processedPeriods[$uniqueKey] = true;

            $batch = $schedule->batchSubject->batch ?? null;
            $supervisor = optional($batch?->batchEmployees->first())->employee;
            $type = $typeService->resolve($schedule);

            $periods[$dayKey][$periodKey][] = [
                'batch_name' => $batch?->name,
                'subject' => $schedule->batchSubject->subject->name ?? null,
                'class_room' => $schedule->classRoom->name ?? null,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'is_default' => $schedule->is_default,
                'type' => $type, // الحقل الجديد
                'supervisor' => $supervisor ? [
                    'name' => "{$supervisor->first_name} {$supervisor->last_name}",
                    'photo' => $supervisor->photo_url,
                ] : null,
            ];
        }

        return [
            'periods_count' => $schedules
                ->pluck('period_number')
                ->unique()
                ->count(),
            'periods' => $periods,
        ];
    }
}
