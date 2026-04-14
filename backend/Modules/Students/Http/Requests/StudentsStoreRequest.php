<?php

namespace Modules\Students\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentsStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institute_branch_id'    => 'nullable|integer|exists:institute_branches,id',
            'family_id'              => 'nullable|integer|exists:families,id',
            'user_id'                => 'nullable|integer|exists:users,id|unique:students,user_id',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'date_of_birth'          => 'nullable|date|before:today',
            'birth_place'            => 'nullable|string|max:255',
            'profile_photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'branch_id'              => 'nullable|integer|exists:academic_branches,id',
            'enrollment_date'        => 'nullable|date',
            'start_attendance_date'  => 'nullable|date|after_or_equal:enrollment_date',
            'gender'                 => 'nullable|in:male,female',
            'previous_school_name'   => 'nullable|string|max:255',
            'national_id'            => 'nullable|string|max:50',
            'how_know_institute'     => 'nullable|string|max:255',
            'bus_id'                 => 'nullable|integer|exists:buses,id',
            'notes'                  => 'nullable|string',

            'school_id'              => 'nullable|integer|exists:schools,id',

            'health_status'          => 'nullable|string|max:255',
            'psychological_status'   => 'nullable|string|max:255',

            'status_id'              => 'nullable|integer|exists:student_statuses,id',
            'city_id'                => 'nullable|integer|exists:cities,id',
            'qr_code_data'           => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'institute_branch_id.exists'   => 'فرع المعهد المحدد غير موجود.',

            'branch_id.exists'   => 'فرع الطالب الدراسي المحدد غير موجود.',

            'family_id.exists' => 'العائلة المحددة غير موجودة.',

            'user_id.exists'   => 'المستخدم المحدد غير موجود.',
            'user_id.unique'   => 'هذا المستخدم مرتبط بطالب آخر.',

            'first_name.required' => 'الاسم الأول مطلوب.',
            'first_name.string'   => 'الاسم الأول يجب أن يكون نصاً.',
            'first_name.max'      => 'الاسم الأول يجب ألا يتجاوز 255 حرفاً.',

            'last_name.required' => 'الكنية مطلوبة.',
            'last_name.string'   => 'الكنية يجب أن تكون نصاً.',
            'last_name.max'      => 'الكنية يجب ألا تتجاوز 255 حرفاً.',

            'date_of_birth.date'    => 'تاريخ الميلاد غير صالح.',
            'date_of_birth.before'  => 'تاريخ الميلاد يجب أن يكون في الماضي.',

            'enrollment_date.date'     => 'تاريخ التسجيل غير صالح.',
            'school_id.exists'         => 'المدرسة المحددة غير موجودة.',

            'start_attendance_date.date'           => 'تاريخ بدء الحضور غير صالح.',
            'start_attendance_date.after_or_equal' => 'تاريخ بدء الحضور يجب أن يكون بعد أو يساوي تاريخ التسجيل.',

            'gender.in' => 'الجنس يجب أن يكون "male" أو "female".',

            'national_id.max' => 'الرقم الوطني يجب ألا يتجاوز 50 حرفاً.',

            'bus_id.exists' => 'الباص المحدد غير موجود.',

            'status_id.exists' => 'حالة الطالب المحددة غير موجودة.',

            'city_id.exists' => 'المدينة المحددة غير موجودة.',

            'health_status.string'        => 'الحالة الصحية يجب أن تكون نصاً.',
            'health_status.max'           => 'الحالة الصحية يجب ألا تتجاوز 255 حرفاً.',
            'psychological_status.string' => 'الحالة النفسية يجب أن تكون نصاً.',
            'psychological_status.max'    => 'الحالة النفسية يجب ألا تتجاوز 255 حرفاً.',
        ];
    }
}
