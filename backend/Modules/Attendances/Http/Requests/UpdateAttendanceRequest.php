<?php

namespace Modules\Attendances\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'institute_branch_id' => 'sometimes|required|integer|exists:institute_branches,id',
            'student_id' => 'sometimes|required|integer|exists:students,id',
            'batch_id' => 'sometimes|required|integer|exists:batches,id',
            'attendance_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:present,absent,late',
            'recorded_by' => 'sometimes|required|integer|exists:users,id',
            'device_id' => 'sometimes|nullable|string|max:100',
            'recorded_at' => 'sometimes|nullable|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages()
    {
        return [
            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists' => 'فرع المعهد غير موجود',
            'student_id.required' => 'معرف الطالب مطلوب',
            'student_id.exists' => 'الطالب غير موجود',
            'batch_id.required' => 'معرف الشعبة/الدورة مطلوب',
            'batch_id.exists' => 'الشعبة/الدورة غير موجودة',
            'attendance_date.required' => 'تاريخ الحضور مطلوب',
            'attendance_date.date' => 'تاريخ الحضور يجب أن يكون تاريخ صالح',
            'status.required' => 'حالة الحضور مطلوبة',
            'status.in' => 'حالة الحضور يجب أن تكون من القيم: present, absent, late',
            'recorded_by.required' => 'معرف المسجل مطلوب',
            'recorded_by.exists' => 'المسجل غير موجود',
            'device_id.max' => 'معرف الجهاز يجب ألا يتجاوز 100 حرف',
            'recorded_at.date_format' => 'صيغة وقت التسجيل يجب أن تكون Y-m-d H:i:s',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422));
    }
}