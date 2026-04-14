<?php

namespace Modules\ClassSchedules\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'batch_subject_id' => $this->batch_subject_id,
            'batch_subject' => $this->batchSubject ? [
                'id' => $this->batchSubject->id,
                'batch' => $this->batchSubject->batch ? [
                    'id' => $this->batchSubject->batch->id,
                    'name' => $this->batchSubject->batch->name,
                ] : null,
                'subject' => $this->batchSubject->subject ? [
                    'id' => $this->batchSubject->subject->id,
                    'name' => $this->batchSubject->subject->name,
                ] : null,
                'instructor_subject' => $this->batchSubject->instructorSubject ? [
                    'id' => $this->batchSubject->instructorSubject->id,
                    'instructor' => $this->batchSubject->instructorSubject->instructor ? [
                        'id' => $this->batchSubject->instructorSubject->instructor->id,
                        'name' => $this->batchSubject->instructorSubject->instructor->name,
                    ] : null,
                ] : null,
            ] : null,

       
            'day_of_week'   => $this->day_of_week,
            'schedule_date' => optional($this->schedule_date)->toDateString(),

         
            'period_number' => $this->period_number,
            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,

          
            'class_room' => $this->class_room_id
                ? [
                    'id'   => $this->classRoom->id,
                    'name' => $this->classRoom->name,
                    'code' => $this->classRoom->code,
                ]
                : null,

            'is_default' => (bool) $this->is_default,
            'is_active'  => (bool) $this->is_active,

            'description' => $this->description,

           
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
