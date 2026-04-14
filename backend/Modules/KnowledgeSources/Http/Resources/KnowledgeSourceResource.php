<?php

namespace Modules\KnowledgeSources\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class KnowledgeSourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "is_active" => $this->is_active,
            "created_at" => $this->created_at?->toDateTimeString(),
            "updated_at" => $this->updated_at?->toDateTimeString(),
        ];
    }
}
