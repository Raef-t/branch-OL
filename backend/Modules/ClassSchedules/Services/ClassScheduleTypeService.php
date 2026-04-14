<?php

namespace Modules\ClassSchedules\Services;

use Illuminate\Support\Collection;
use Modules\ClassSchedules\Models\ClassSchedule;
use Carbon\Carbon;

class ClassScheduleTypeService
{
    /**
     * @var Collection
     */
    protected Collection $todayExams;

    /**
     * نستقبل امتحانات اليوم مرة واحدة
     */
    public function __construct(Collection $todayExams)
    {
        $this->todayExams = $todayExams;
    }

    /**
     * تحديد نوع الحصة: درس أو اختبار
     */
    public function resolve(ClassSchedule $schedule): string
    {
        $hasExam = $this->todayExams->contains(function ($exam) use ($schedule) {
            return
                $exam->batch_subject_id === $schedule->batch_subject_id &&
                $exam->exam_time === $schedule->start_time &&
                $exam->batchSubject?->class_room_id === $schedule->class_room_id;
        });

        return $hasExam ? 'اختبار' : 'درس';
    }
}
