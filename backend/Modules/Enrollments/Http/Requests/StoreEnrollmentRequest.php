<?php

namespace Modules\Enrollments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ========== بيانات الطالب ==========
            'student.first_name'             => 'required|string|max:255',
            'student.last_name'              => 'required|string|max:255',
            'student.date_of_birth'          => 'nullable|date|before:today',
            'student.birth_place'            => 'nullable|string|max:255',

            'student.profile_photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student.id_card_photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'student.institute_branch_id'    => 'nullable|integer|exists:institute_branches,id',
            'student.branch_id'              => 'nullable|integer|exists:academic_branches,id',
            'student.enrollment_date'        => 'nullable|date',
            'student.start_attendance_date'  => 'nullable|date|after_or_equal:student.enrollment_date',
            'student.gender'                 => 'nullable|in:male,female',
            'student.previous_school_name'   => 'nullable|string|max:255',
            'student.national_id'            => 'nullable|string|max:50',
            'student.how_know_institute'     => 'nullable|string|max:255',
            'student.bus_id'                 => 'nullable|integer|exists:buses,id',
            'student.school_id'              => 'nullable|integer|exists:schools,id',
            'student.notes'                  => 'nullable|string',

          
            'student.health_status'          => 'nullable|string|max:255',
            'student.psychological_status'   => 'nullable|string|max:255',

            'student.status_id'              => 'nullable|integer|exists:student_statuses,id',
            'student.city_id'                => 'nullable|integer|exists:cities,id',
            'student.qr_code_data'           => 'nullable|string',

            // ========== بيانات الأب ==========
            'father.first_name'              => 'required|string|max:255',
            'father.last_name'               => 'required|string|max:255',
            'father.national_id'             => 'nullable|string|max:50',
            'father.phone'                   => 'nullable|string|max:20',
            'father.occupation'              => 'nullable|string|max:255',
            'father.address'                 => 'nullable|string',

            // ========== بيانات الأم ==========
            'mother.first_name'              => 'required|string|max:255',
            'mother.last_name'               => 'required|string|max:255',
            'mother.national_id'             => 'nullable|string|max:50',
            'mother.phone'                   => 'nullable|string|max:20',
            'mother.occupation'              => 'nullable|string|max:255',
            'mother.address'                 => 'nullable|string',

            // ========== خيارات أخرى ==========
            'is_existing_family_confirmed'   => 'nullable|boolean',
            'confirmed_family_id'            => 'nullable|integer|exists:families,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_existing_family_confirmed')) {
            $this->merge([
                'is_existing_family_confirmed' => filter_var($this->is_existing_family_confirmed, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }

    public function messages(): array
    {
        return [
            // ======== بيانات الطالب ========
            'student.first_name.required' => 'الاسم الأول للطالب مطلوب.',
            'student.first_name.string'   => 'الاسم الأول يجب أن يكون نصاً.',
            'student.first_name.max'      => 'الاسم الأول لا يمكن أن يتجاوز 255 حرفاً.',

            'student.last_name.required'  => 'الكنية للطالب مطلوبة.',
            'student.last_name.string'    => 'الكنية يجب أن تكون نصاً.',
            'student.last_name.max'       => 'الكنية لا يمكن أن تتجاوز 255 حرفاً.',

            'student.date_of_birth.date'  => 'تاريخ الميلاد غير صالح.',
            'student.date_of_birth.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم.',

            'student.birth_place.string'  => 'مكان الولادة يجب أن يكون نصاً.',
            'student.birth_place.max'     => 'مكان الولادة لا يمكن أن يتجاوز 255 حرفاً.',

            'student.profile_photo.image' => 'صورة الطالب يجب أن تكون ملف صورة.',
            'student.profile_photo.mimes' => 'صورة الطالب يجب أن تكون من نوع jpeg أو png أو jpg أو gif.',
            'student.profile_photo.max'   => 'حجم صورة الطالب لا يجب أن يتجاوز 2 ميغابايت.',

            'student.id_card_photo.image' => 'صورة الهوية يجب أن تكون ملف صورة أو PDF.',
            'student.id_card_photo.mimes' => 'الهوية يجب أن تكون من نوع jpeg أو png أو jpg أو gif أو pdf.',
            'student.id_card_photo.max'   => 'حجم صورة الهوية لا يجب أن يتجاوز 2 ميغابايت.',

            'student.institute_branch_id.required' => 'فرع المعهد للطالب مطلوب.',
            'student.institute_branch_id.exists'   => 'الفرع المحدد للمعهد غير موجود.',

            'student.branch_id.required'  => 'الفرع الدراسي للطالب مطلوب.',
            'student.branch_id.exists'    => 'الفرع الدراسي المحدد غير موجود.',

            'student.enrollment_date.date'     => 'تاريخ التسجيل غير صالح.',

            'student.start_attendance_date.date'            => 'تاريخ بدء الحضور غير صالح.',
            'student.start_attendance_date.after_or_equal'  => 'تاريخ بدء الحضور يجب أن يكون بعد أو يساوي تاريخ التسجيل.',

            'student.gender.in' => 'الجنس يجب أن يكون إما male أو female.',

            'student.previous_school_name.string' => 'اسم المدرسة السابقة يجب أن يكون نصاً.',
            'student.previous_school_name.max'    => 'اسم المدرسة السابقة لا يمكن أن يتجاوز 255 حرفاً.',

            'student.national_id.string' => 'الرقم الوطني يجب أن يكون نصاً.',
            'student.national_id.max'    => 'الرقم الوطني لا يمكن أن يتجاوز 50 حرفاً.',

            'student.how_know_institute.string' => 'طريقة معرفة المعهد يجب أن تكون نصاً.',
            'student.how_know_institute.max'    => 'طريقة معرفة المعهد لا يمكن أن تتجاوز 255 حرفاً.',

            'student.bus_id.exists'  => 'الباص المحدد غير موجود.',
            'student.notes.string'   => 'الملاحظات يجب أن تكون نصاً.',

            'student.status_id.exists' => 'حالة الطالب المحددة غير موجودة.',
            'student.city_id.exists'   => 'المدينة المحددة غير موجودة.',

            // ✅ الرسائل الجديدة
            'student.health_status.string'        => 'الحالة الصحية يجب أن تكون نصاً.',
            'student.health_status.max'           => 'الحالة الصحية يجب ألا تتجاوز 255 حرفاً.',
            'student.psychological_status.string' => 'الحالة النفسية يجب أن تكون نصاً.',
            'student.psychological_status.max'    => 'الحالة النفسية يجب ألا تتجاوز 255 حرفاً.',

            'student.qr_code_data.string' => 'بيانات رمز QR يجب أن تكون نصاً.',

            // ======== باقي الرسائل كما كانت ========
            'father.first_name.required' => 'الاسم الأول للأب مطلوب.',
            'father.last_name.required'  => 'الكنية للأب مطلوبة.',
            'mother.first_name.required' => 'الاسم الأول للأم مطلوب.',
            'mother.last_name.required'  => 'الكنية للأم مطلوبة.',

            'is_existing_family_confirmed.boolean' => 'قيمة حقل تأكيد العائلة يجب أن تكون true أو false فقط.',
            'confirmed_family_id.integer'           => 'معرف العائلة يجب أن يكون رقماً صحيحاً.',
            'confirmed_family_id.exists'            => 'العائلة المحددة غير موجودة في النظام.',
        ];
    }
}
