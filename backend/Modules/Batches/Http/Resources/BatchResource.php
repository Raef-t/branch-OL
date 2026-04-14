<?php

namespace Modules\Batches\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            /* =========================
             * Institute Branch
             * ========================= */
            'institute_branch' => $this->whenLoaded('instituteBranch', function () {
                return [
                    'id'      => $this->instituteBranch->id,
                    'name'    => $this->instituteBranch->name,
                    'code'    => $this->instituteBranch->code,
                    'address' => $this->instituteBranch->address,
                    'is_active' => $this->instituteBranch->is_active,
                ];
            }),

            /* =========================
             * Academic Branch
             * ========================= */
            'academic_branch' => $this->whenLoaded('academicBranch', function () {
                return [
                    'id'          => $this->academicBranch->id,
                    'name'        => $this->academicBranch->name,
                    'description' => $this->academicBranch->description,
                ];
            }),

            /* =========================
             * Class Room
             * ========================= */
            'class_room' => $this->whenLoaded('classRoom', function () {
                return [
                    'id'   => $this->classRoom->id,
                    'name' => $this->classRoom->name,
                    'code' => $this->classRoom->code,
                ];
            }),

            /* =========================
             * Batch Info
             * ========================= */
            'name'        => $this->name,
            'start_date'  => $this->start_date,
            'end_date'    => $this->end_date,
            'gender_type' => $this->gender_type,

            'is_archived'  => $this->is_archived,
            'is_hidden'    => $this->is_hidden,
            'is_completed' => $this->is_completed,

            /* =========================
             * Counts (محملة عبر withCount)
             * ========================= */
            'students_count'    => $this->when(isset($this->batch_students_count), $this->batch_students_count),
            'subjects_count'    => $this->when(isset($this->batch_subjects_count), $this->batch_subjects_count),
            'employees_count'   => $this->when(isset($this->batch_employees_count), $this->batch_employees_count),
            'instructors_count' => $this->when(isset($this->instructors_count), $this->instructors_count),

            /* =========================
             * Employees (الموظفون المعينون)
             * ========================= */
            'employees' => $this->whenLoaded('batchEmployees', function () {
                return $this->batchEmployees->map(function ($batchEmployee) {
                    return [
                        'id'              => $batchEmployee->id,
                        'employee_id'     => $batchEmployee->employee_id,
                        'employee_name'   => $batchEmployee->employee?->full_name ?? $batchEmployee->employee?->first_name,
                        'role'            => $batchEmployee->role,
                        'is_active'       => $batchEmployee->is_active,
                        'assignment_date' => $batchEmployee->assignment_date,
                    ];
                });
            }),

            'batch_subjects' => $this->whenLoaded('batchSubjects', function () {
                return $this->batchSubjects->map(function ($bs) {
                    return [
                        'id' => $bs->id,
                        'subject_id' => $bs->subject_id,
                        'subject' => $bs->subject ? [
                            'id' => $bs->subject->id,
                            'name' => $bs->subject->name,
                        ] : null,
                    ];
                });
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

