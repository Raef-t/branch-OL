<?php

namespace Modules\Subjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $subjectId = $this->route('id'); // معرف المادة الحالي

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')
                    ->where(fn ($q) =>
                        $q->where('academic_branch_id', $this->academic_branch_id)
                    )
                    ->ignore($subjectId), // ✅ تجاهل السجل الحالي
            ],

            'description' => 'nullable|string',

            'academic_branch_id' => 'sometimes|required|exists:academic_branches,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'اسم المادة مطلوب',
            'name.unique'   => 'اسم المادة موجود مسبقًا ضمن هذا الفرع الأكاديمي',
            'name.max'      => 'اسم المادة لا يمكن أن يتجاوز 255 حرف',

            'academic_branch_id.required' => 'الفرع الأكاديمي للمادة مطلوب',
            'academic_branch_id.exists'   => 'الفرع الأكاديمي المحدد غير موجود',
        ];
    }
}
