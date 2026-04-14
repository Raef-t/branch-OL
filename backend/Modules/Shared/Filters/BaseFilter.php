<?php

namespace Modules\Shared\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class BaseFilter
{
    public static function fromRequest(Request $request): static
    {
        $validator = Validator::make(
            $request->query(),
            static::rules(),
            static::messages()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return static::make($request);
    }

    /**
     * Factory method to create the filter instance.
     */
    abstract protected static function make(Request $request): static;

    /**
     * Define validation rules for query parameters.
     */
    abstract protected static function rules(): array;

    /**
     * Optional custom error messages.
     */
    protected static function messages(): array
    {
        return [];
    }
}
