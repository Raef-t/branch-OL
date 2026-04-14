<?php

namespace Modules\Subjects\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,

            // ✅ academic branch (id + name)
            'academic_branch' => [
                'id'   => $this->academicBranch->id ?? null,
                'name' => $this->academicBranch->name ?? null,
            ],

            'name'        => $this->name,
            'description' => $this->description,

            'created_at' => $this->created_at
                ? $this->created_at->toDateTimeString()
                : null,

            'updated_at' => $this->updated_at
                ? $this->updated_at->toDateTimeString()
                : null,
        ];
    }
}
