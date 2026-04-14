<?php

declare(strict_types=1);

namespace Modules\Students\Services;

use Illuminate\Support\Collection;
use Modules\ClassSchedules\Models\ClassSchedule;
use Modules\Exams\Models\Exam;

final class ClassScheduleTypeService
{
    public function __construct(
        private Collection $exams
    ) {}

    public function resolve(ClassSchedule $schedule): string
    {
        $hasExam = $this->exams->contains(function ($exam) use ($schedule) {
            // ✅ التحقق من وجود العلاقات قبل استخدامها
            $batchSubject = optional($exam->batchSubject);

            return
                $exam->batch_subject_id == $schedule->batch_subject_id &&
                $exam->exam_time == $schedule->start_time &&
                $batchSubject->class_room_id == $schedule->class_room_id;
        });

        return $hasExam ? 'امتحان' : 'درس';
    }
}
