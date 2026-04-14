<?php

namespace Modules\Batches\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBatchRequest extends FormRequest
{
    public function authorize()
    {
        return true; // إذا عندك صلاحيات تحقق ضعها هنا
    }

    public function rules()
    {
        return [
            'institute_branch_id' => 'nullable|integer|exists:institute_branches,id',
            'academic_branch_id'  => 'nullable|integer|exists:academic_branches,id',

            // 👇 القاعة
            'class_room_id'       => 'nullable|integer|exists:class_rooms,id',

            'name'        => 'required|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',

            'gender_type' => 'nullable|in:male,female,mixed',

            'is_archived'  => 'nullable|boolean',
            'is_hidden'    => 'nullable|boolean',
            'is_completed' => 'nullable|boolean',
        ];
    }


    public function messages()
    {
        return [
            'institute_branch_id.required' => 'معرف فرع المعهد مطلوب',
            'institute_branch_id.exists' => 'فرع المعهد غير موجود',

            'academic_branch_id.required' => 'معرف التخصص الأكاديمي مطلوب',
            'academic_branch_id.exists' => 'التخصص الأكاديمي غير موجود',

            'name.required' => 'اسم الشعبة/الدورة مطلوب',
            'name.max' => 'اسم الشعبة/الدورة يجب ألا يتجاوز 255 حرفًا',

            'gender_type.required' => 'نوع الجنس للشعبة/الدورة مطلوب',
            'gender_type.in'       => 'نوع الجنس يجب أن يكون male أو female أو mixed',


            'start_date.required' => 'تاريخ البداية مطلوب',
            'start_date.date' => 'تاريخ البداية يجب أن يكون تاريخ صالح',

            'end_date.required' => 'تاريخ النهاية مطلوب',
            'end_date.date' => 'تاريخ النهاية يجب أن يكون تاريخ صالح',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية',

            'is_archived.boolean' => 'حالة الأرشفة يجب أن تكون صحيح أو خطأ',
            'is_hidden.boolean' => 'حالة الإخفاء يجب أن تكون صحيح أو خطأ',
            'is_completed.boolean' => 'حالة الاكتمال يجب أن تكون صحيح أو خطأ',
            'class_room_id.required' => 'القاعة مطلوبة',
            'class_room_id.exists'   => 'القاعة غير موجودة',

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
