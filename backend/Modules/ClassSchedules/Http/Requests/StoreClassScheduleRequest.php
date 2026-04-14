<?php

namespace Modules\ClassSchedules\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClassScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'batch_subject_id' => 'required|integer|exists:batch_subjects,id',

            // 🗓 إما أسبوعي أو بتاريخ محدد (ليس كلاهما)
            'day_of_week' => 'nullable|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'schedule_date' => 'nullable|date',

            // 🕐 رقم الحصة (1–5)
            'period_number' => 'required|integer|min:1|max:5',

            // ⏱ التوقيت (حالياً إلزامي – يمكن جعله مشتق لاحقاً)
            'start_time' => 'required|date_format:H:i:s',
            'end_time'   => 'required|date_format:H:i:s|after:start_time',

            // 🏫 قاعة استثنائية
            'class_room_id' => 'nullable|integer|exists:class_rooms,id',

            // ⚙️ الحالة
            'is_default' => 'nullable|boolean',
            'is_active'  => 'nullable|boolean',

            'description' => 'nullable|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

          
            if ($this->filled('day_of_week') && $this->filled('schedule_date')) {
                $validator->errors()->add(
                    'day_of_week',
                    'لا يمكن تحديد يوم أسبوع وتاريخ محدد معاً'
                );
            }

       
            if (!$this->filled('day_of_week') && !$this->filled('schedule_date')) {
                $validator->errors()->add(
                    'day_of_week',
                    'يجب تحديد يوم أسبوع أو تاريخ محدد'
                );
            }
        });
    }

    public function messages()
    {
        return [
            'batch_subject_id.required' => 'معرف مادة الدفعة مطلوب',
            'batch_subject_id.exists'   => 'المادة المرتبطة بالدفعة غير موجودة',

            'day_of_week.in' => 'يوم الأسبوع غير صالح',
            'schedule_date.date' => 'التاريخ يجب أن يكون صالحاً',

            'period_number.required' => 'رقم الحصة مطلوب',
            'period_number.min' => 'رقم الحصة يجب أن يكون بين 1 و 5',
            'period_number.max' => 'رقم الحصة يجب أن يكون بين 1 و 5',

            'start_time.required' => 'وقت البداية مطلوب',
            'start_time.date_format' => 'صيغة وقت البداية يجب أن تكون HH:MM:SS',

            'end_time.required' => 'وقت النهاية مطلوب',
            'end_time.date_format' => 'صيغة وقت النهاية يجب أن تكون HH:MM:SS',
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية',

            'class_room_id.exists' => 'القاعة المحددة غير موجودة',

            'is_default.boolean' => 'قيمة الافتراضي يجب أن تكون true أو false',
            'is_active.boolean'  => 'قيمة التفعيل يجب أن تكون true أو false',

            'description.max' => 'الوصف يجب ألا يتجاوز 255 حرفاً',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
