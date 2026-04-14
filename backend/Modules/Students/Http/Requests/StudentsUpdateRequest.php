<?php

namespace Modules\Students\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('id');

        return [
            'institute_branch_id'    => 'nullable|integer|exists:institute_branches,id',
            'family_id'              => 'nullable|integer|exists:families,id',
            'user_id'                => 'nullable|integer|exists:users,id|unique:students,user_id,' . $studentId,
            'first_name'             => 'nullable|string|max:255',
            'last_name'              => 'nullable|string|max:255',
            'school_id'              => 'nullable|integer|exists:schools,id',
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


            'health_status'          => 'nullable|string|max:255',
            'psychological_status'   => 'nullable|string|max:255',

            'status_id'              => 'nullable|integer|exists:student_statuses,id',
            'city_id'                => 'nullable|integer|exists:cities,id',
            'qr_code_data'           => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return array_merge(
            (new StudentsStoreRequest())->messages(),
            [
                'school_id.exists' => 'المدرسة المحددة غير موجودة.',

                'health_status.string'        => 'الحالة الصحية يجب أن تكون نصاً.',
                'health_status.max'           => 'الحالة الصحية يجب ألا تتجاوز 255 حرفاً.',
                'psychological_status.string' => 'الحالة النفسية يجب أن تكون نصاً.',
                'psychological_status.max'    => 'الحالة النفسية يجب ألا تتجاوز 255 حرفاً.',
            ]
        );
    }
}
