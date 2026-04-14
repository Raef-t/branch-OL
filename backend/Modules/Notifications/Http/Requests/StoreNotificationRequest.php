<?php

namespace Modules\Notifications\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'body' => ['required', 'string', 'min:5', 'max:2000'],
            'template_id' => ['nullable', 'integer', 'exists:message_templates,id'],
            'sender_id' => ['nullable', 'integer', 'exists:users,id'],
            'sender_type' => ['nullable', 'string', 'in:admin,system,user'],
            'target_snapshot.type' => ['required', 'string', 'in:all,branch,batch,custom'],
            'target_snapshot.user_ids' => ['required_if:target_snapshot.type,custom', 'array'],
            'target_snapshot.user_ids.*' => ['integer', 'exists:users,id', 'distinct'],
            'target_snapshot.branch_id' => ['required_if:target_snapshot.type,branch', 'nullable', 'integer', 'exists:institute_branches,id'],
            'target_snapshot.batch_id' => ['required_if:target_snapshot.type,batch', 'nullable', 'integer', 'exists:batches,id'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,mp4,mp3'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'حقل العنوان مطلوب.',
            'title.string' => 'حقل العنوان يجب أن يكون نصًا.',
            'title.max' => 'حقل العنوان يجب ألا يتجاوز :max حرفًا.',
            'title.min' => 'حقل العنوان يجب أن يحتوي على الأقل :min أحرف.',

            'body.required' => 'حقل المحتوى مطلوب.',
            'body.string' => 'حقل المحتوى يجب أن يكون نصًا.',
            'body.min' => 'حقل المحتوى يجب أن يحتوي على الأقل :min أحرف.',
            'body.max' => 'حقل المحتوى يجب ألا يتجاوز :max حرفًا.',

            'template_id.exists' => 'القالب المحدد غير موجود.',

            'sender_id.exists' => 'المرسل المحدد غير موجود.',
            'sender_type.in' => 'نوع المرسل يجب أن يكون (admin, system, user).',

            'target_snapshot.type.required' => 'حقل نوع المستهدفين مطلوب.',
            'target_snapshot.type.in' => 'نوع المستهدفين يجب أن يكون (all, branch, batch, custom).',

            'target_snapshot.user_ids.required_if' => 'حقل معرفات المستخدمين مطلوب عند اختيار نوع المستهدفين "مخصص".',
            'target_snapshot.user_ids.array' => 'حقل معرفات المستخدمين يجب أن يكون مصفوفة.',
            'target_snapshot.user_ids.*.integer' => 'كل معرف مستخدم يجب أن يكون رقمًا صحيحًا.',
            'target_snapshot.user_ids.*.exists' => 'بعض معرفات المستخدمين غير موجودة.',
            'target_snapshot.user_ids.*.distinct' => 'لا يمكن تكرار معرفات المستخدمين.',

            'target_snapshot.branch_id.required_if' => 'حقل الفرع مطلوب عند اختيار نوع المستفين "فرع".',
            'target_snapshot.branch_id.exists' => 'الفرع المحدد غير موجود.',
            
            'target_snapshot.batch_id.required_if' => 'حقل الدورة/الشعبة مطلوب عند اختيار نوع المستهدفين "شعبة".',
            'target_snapshot.batch_id.exists' => 'الدورة/الشعبة المحددة غير موجودة.',

            'attachments.array' => 'حقل المرفقات يجب أن يكون مصفوفة.',
            'attachments.max' => 'يمكنك رفع الحد الأقصى :max مرفقات فقط.',
            'attachments.*.file' => 'كل مرفق يجب أن يكون ملفًا.',
            'attachments.*.max' => 'كل مرفق يجب ألا يتجاوز :max كيلوبايت.',
            'attachments.*.mimes' => 'نوع الملف غير مدعوم. الأنواع المسموحة: :values.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'العنوان',
            'body' => 'المحتوى',
            'template_id' => 'القالب',
            'sender_id' => 'المرسل',
            'sender_type' => 'نوع المرسل',
            'target_snapshot.type' => 'نوع المستهدفين',
            'target_snapshot.user_ids' => 'قائمة المستخدمين',
            'target_snapshot.branch_id' => 'الفرع',
            'target_snapshot.batch_id' => 'الشعبة/الدورة',
            'attachments' => 'المرفقات',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            // التحقق من وجود المستخدمين عند اختيار نوع "مخصص"
            if (isset($data['target_snapshot']['type']) && $data['target_snapshot']['type'] === 'custom') {
                if (empty($data['target_snapshot']['user_ids']) || count($data['target_snapshot']['user_ids']) === 0) {
                    $validator->errors()->add(
                        'target_snapshot.user_ids',
                        'يجب تحديد على الأقل مستخدم واحد عند اختيار نوع المستهدفين "مخصص".'
                    );
                }
            }

            // التحقق من وجود فرع عند اختيار نوع "فرع"
            if (isset($data['target_snapshot']['type']) && $data['target_snapshot']['type'] === 'branch') {
                if (empty($data['target_snapshot']['branch_id'])) {
                    $validator->errors()->add(
                        'target_snapshot.branch_id',
                        'يجب تحديد فرع عند اختيار نوع المستهدفين "فرع".'
                    );
                }
            }

            // التحقق من وجود شعبة عند اختيار نوع "شعبة"
            if (isset($data['target_snapshot']['type']) && $data['target_snapshot']['type'] === 'batch') {
                if (empty($data['target_snapshot']['batch_id'])) {
                    $validator->errors()->add(
                        'target_snapshot.batch_id',
                        'يجب تحديد شعبة/دورة عند اختيار نوع المستهدفين "شعبة".'
                    );
                }
            }
        });
    }
}
