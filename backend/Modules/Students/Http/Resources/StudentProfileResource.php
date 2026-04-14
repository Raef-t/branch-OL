<?php

namespace Modules\Students\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\AcademicBranches\Http\Resources\AcademicBranchesResource;
use Modules\AcademicRecords\Http\Resources\AcademicRecordResource;
use Modules\Attendances\Http\Resources\AttendanceResource;
use Modules\Batches\Http\Resources\BatchResource;
use Modules\Buses\Http\Resources\BusesResource;
use Modules\Cities\Http\Resources\CityResource;
use Modules\ExamResults\Http\Resources\ExamResultResource;
use Modules\Families\Http\Resources\FamilyResource;
use Modules\InstituteBranches\Http\Resources\InstituteBranchResource;
use Modules\Payments\Http\Resources\PaymentResource;
use Modules\StudentStatuses\Http\Resources\StudentStatusResource;

class StudentProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'school_id'             => $this->school_id,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'profile_photo' => $this->profile_photo_url,
            'id_card_photo' => $this->id_card_photo_url,
            'city' => new CityResource($this->whenLoaded('city')),
            'family' => new FamilyResource($this->whenLoaded('family')),
            'branch' => new AcademicBranchesResource($this->whenLoaded('branch')),
            'institute_branch' => new InstituteBranchResource($this->whenLoaded('instituteBranch')),
            'status' => new StudentStatusResource($this->whenLoaded('status')),
            'bus' => new BusesResource($this->whenLoaded('bus')),
            'academic_records' => AcademicRecordResource::collection($this->whenLoaded('academicRecords')),
            //'contracts' => EnrollmentContractResource::collection($this->whenLoaded('contracts')),
            //  'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'exam_results' => ExamResultResource::collection($this->whenLoaded('examResults')),
            'latest_attendance' => new AttendanceResource($this->whenLoaded('latestAttendance')),

            'batches' => BatchResource::collection($this->whenLoaded('batches')),
            'school' => new \Modules\Schools\Http\Resources\SchoolResource($this->whenLoaded('school')),
        ];
    }
}
