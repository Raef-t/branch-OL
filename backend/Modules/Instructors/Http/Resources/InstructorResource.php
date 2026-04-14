<?php

namespace Modules\Instructors\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'institute_branch' => $this->whenLoaded('instituteBranch', function () {
                return [
                    'id'   => $this->instituteBranch->id,
                    'name' => $this->instituteBranch->name,
                ];
            }),


            'phone' => $this->phone,
            'specialization' => $this->specialization,
            'hire_date' => $this->hire_date,
            'profile_photo_url' => $this->profile_photo_url,
            'preferences' => $this->preferences,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
