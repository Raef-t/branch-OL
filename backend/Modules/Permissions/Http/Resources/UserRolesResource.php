<?php
namespace Modules\Permissions\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRolesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ];
    }
}
