<?php

namespace Modules\Employees\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'job_title' => $this->job_title,
            'job_type' => $this->job_type,
            'hire_date' => $this->hire_date,
            'phone' => $this->phone,
            'photo_url' => $this->photo_url,

            'institute_branch_id' => $this->institute_branch_id,
            'is_active' => $this->is_active,
               
            'institute_branch' => $this->whenLoaded('instituteBranch', function () {
                return [
                    'id' => $this->instituteBranch->id,
                    'name' => $this->instituteBranch->name,
                    'address' => $this->instituteBranch->address,
                    'code' => $this->instituteBranch->code,
                    'phone' => $this->instituteBranch->phone,
                    'email' => $this->instituteBranch->email,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'unique_id' => $this->user->unique_id,
                    'roles' => $this->user->roles->pluck('name'),
                    'is_approved' => $this->user->is_approved,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}