<?php

namespace Modules\Families\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class FamilyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'user_id' => $this->user_id,
            'user'    => $this->user ? [
                'id'         => $this->user->id,
                'unique_id'  => $this->user->unique_id,
                'name'       => $this->user->name,
                'role'       => $this->user->role,
                'is_approved'=> (bool)$this->user->is_approved,
            ] : null,   
            'students_count'  => $this->students?->count() ?? 0,
            'guardians_count' => $this->guardians?->count() ?? 0,
            
            'students' => $this->students ? $this->students->map(function ($student) {
                return [
                    'id'        => $student->id,
                    'full_name' => $student->full_name,
                    'user'      => $student->user_id && $student->user ? [
                        'id'         => $student->user->id,
                        'unique_id'  => $student->user->unique_id,
                        'is_approved'=> (bool)$student->user->is_approved,
                    ] : null,
                ];
            }) : null,

            'guardians'  => $this->whenLoaded('guardians'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
