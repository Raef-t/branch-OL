<?php

namespace Modules\AcademicBranches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicBranchesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // عدلها حسب الصلاحيات إذا لزم
    }

    public function rules(): array
    {
        $id = $this->route('id'); // جلب الـ id من الـ route

        return [
            'name'        => 'required|string|max:255|unique:academic_branches,name,' . $id,
            'description' => 'nullable|string',
        ];
    }
}
