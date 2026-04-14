<?php

namespace Modules\InstructorSubjects\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignInstructorToSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return true; // لو في صلاحيات ممكن تتحكم هون
    }

    public function rules()
    {
        return [
            'subject_id' => 'required|integer|exists:subjects,id',
            'instructor_id' => 'required|integer|exists:instructors,id',
        ];
    }

    public function messages()
    {
        return [
            'subject_id.required' => 'معرف المادة مطلوب',
            'subject_id.exists' => 'المادة غير موجودة',
            'instructor_id.required' => 'معرف المدرس مطلوب',
            'instructor_id.exists' => 'المدرس غير موجود',
        ];
    }
}
