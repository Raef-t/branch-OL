<?php

namespace Modules\ExamResults\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterExamResultsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],

            // ✅ كل التواريخ اختيارية
            'date'      => ['nullable', 'date'],

            // ✅ لو استخدمت الفترة: يكفي أن ترسل الاثنين معاً
            // لكن لا نلزم بأي تاريخ إن لم تُرسل أي شيء
            'date_from' => ['nullable', 'date', 'required_with:date_to'],
            'date_to'   => ['nullable', 'date', 'required_with:date_from'],

            'subject_id' => ['nullable', 'integer'],

            'marks_from' => ['nullable', 'numeric', 'min:0'],
            'marks_to'   => ['nullable', 'numeric', 'min:0'],

            // أنسب شيء هنا أن نسمح بـ 0/1 و true/false
            'is_passed'  => ['nullable', 'in:0,1,true,false'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // 🔹 تحقق من منطق العلامات
            $marksFrom = $this->input('marks_from');
            $marksTo   = $this->input('marks_to');

            if (!is_null($marksFrom) && !is_null($marksTo) && $marksFrom > $marksTo) {
                $validator->errors()->add(
                    'marks_from',
                    'قيمة العلامة من يجب أن تكون أقل أو تساوي قيمة العلامة إلى.'
                );
            }

            // 🔹 تحقق من منطق التواريخ في حال أرسلت فترة كاملة
            $dateFrom = $this->input('date_from');
            $dateTo   = $this->input('date_to');

            if (!is_null($dateFrom) && !is_null($dateTo) && $dateFrom > $dateTo) {
                $validator->errors()->add(
                    'date_from',
                    'تاريخ البداية يجب أن يكون قبل أو يساوي تاريخ النهاية.'
                );
            }
        });
    }
}
