<?php

namespace Modules\KnowledgeSources\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKnowledgeSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
                "sometimes",
                "string",
                "max:255",
                Rule::unique("knowledge_sources", "name")->ignore($this->route("id")),
            ],
            "description" => "nullable|string",
            "is_active" => "sometimes|boolean",
        ];
    }
}
