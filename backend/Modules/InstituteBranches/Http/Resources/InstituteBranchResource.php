<?php

namespace Modules\InstituteBranches\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstituteBranchResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'country_code' => $this->country_code,
            'address'      => $this->address,
            'phone'        => $this->phone,
            'email'        => $this->email,
            'manager_name' => $this->manager_name,
            'is_active'    => $this->is_active,
            // 'created_at'   => $this->created_at,
            // 'updated_at'   => $this->updated_at,
        ];
    }
}