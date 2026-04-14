<?php

namespace Modules\Exams\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreExamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'batch_subject_id' => 'required|integer|exists:batch_subjects,id',
            'name' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'exam_time' => 'required|date_format:H:i',
            'exam_end_time' => 'required|date_format:H:i|after:exam_time',
            'total_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0|lte:total_marks',
            'status' => 'required|in:scheduled,completed,cancelled',
            'exam_type_id' => 'required|integer|exists:exam_types,id',
            'remarks' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [

            'batch_subject_id.required' => 'معرف مادة الدفعة مطلوب',
            'batch_subject_id.exists' => 'مادة الدفعة غير موجودة',
            'name.required' => 'اسم الامتحان مطلوب',
            'name.max' => 'اسم الامتحان يجب ألا يزيد عن 255 حرف',
            'exam_date.required' => 'تاريخ الامتحان مطلوب',
            'exam_date.date' => 'تاريخ الامتحان يجب أن يكون تاريخ صالح',
            'exam_time.required' => 'وقت الامتحان مطلوب',
            'exam_time.date_format' => 'وقت الامتحان يجب أن يكون بالصيغة HH:MM',
            'total_marks.required' => 'الدرجة الكلية مطلوبة',
            'total_marks.integer' => 'الدرجة الكلية يجب أن تكون عدد صحيح',
            'total_marks.min' => 'الدرجة الكلية يجب أن تكون أكبر من 0',
            'passing_marks.required' => 'درجة النجاح مطلوبة',
            'passing_marks.integer' => 'درجة النجاح يجب أن تكون عدد صحيح',
            'passing_marks.min' => 'درجة النجاح يجب ألا تكون سالبة',
            'passing_marks.lte' => 'درجة النجاح يجب ألا تتجاوز الدرجة الكلية',
            'status.required' => 'حالة الامتحان مطلوبة',
            'status.in' => 'حالة الامتحان يجب أن تكون scheduled أو completed أو cancelled',
            'exam_type_id.required' => 'نوع الامتحان مطلوب',
            'remarks.string' => 'الملاحظات يجب أن تكون نص',
            'exam_end_time.required'    => 'وقت نهاية الامتحان مطلوب',
            'exam_end_time.date_format' => 'وقت نهاية الامتحان يجب أن يكون بالصيغة HH:MM',
            'exam_end_time.after'       => 'وقت نهاية الامتحان يجب أن يكون بعد وقت البداية',
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
