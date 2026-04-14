<?php

namespace Modules\Batches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBatchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

public function rules()
{
    return [
        'institute_branch_id' => 'sometimes|nullable|integer|exists:institute_branches,id',
        'academic_branch_id'  => 'sometimes|nullable|integer|exists:academic_branches,id',

        
        'class_room_id' => 'sometimes|nullable|integer|exists:class_rooms,id',

        'name'       => 'sometimes|required|string|max:255',
        'start_date' => 'sometimes|nullable|date',
        'end_date'   => 'sometimes|nullable|date|after_or_equal:start_date',

        'gender_type' => 'sometimes|nullable|in:male,female,mixed',

        'is_archived'  => 'sometimes|nullable|boolean',
        'is_hidden'    => 'sometimes|nullable|boolean',
        'is_completed' => 'sometimes|nullable|boolean',
    ];
}


    public function messages()
    {
        return [

            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists'   => 'فرع المعهد غير موجود',

            'academic_branch_id.required' => 'معرف التخصص الأكاديمي مطلوب',
            'academic_branch_id.exists'   => 'التخصص الأكاديمي غير موجود',

            'name.required' => 'اسم الشعبة/الدورة مطلوب',
            'name.max'      => 'اسم الشعبة/الدورة يجب ألا يتجاوز 255 حرفًا',

            'start_date.required' => 'تاريخ البداية مطلوب',
            'start_date.date'     => 'تاريخ البداية يجب أن يكون تاريخ صالح',

            'end_date.required'       => 'تاريخ النهاية مطلوب',
            'end_date.date'           => 'تاريخ النهاية يجب أن يكون تاريخ صالح',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',

        
            'gender_type.required' => 'نوع الجنس للشعبة/الدورة مطلوب',
            'gender_type.in'       => 'نوع الجنس يجب أن يكون male أو female أو mixed',

            'is_archived.boolean'  => 'حالة الأرشفة يجب أن تكون صحيح أو خطأ',
            'is_hidden.boolean'    => 'حالة الإخفاء يجب أن تكون صحيح أو خطأ',
            'is_completed.boolean' => 'حالة الاكتمال يجب أن تكون صحيح أو خطأ',
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
