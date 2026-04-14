<?php
namespace Modules\Instructors\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstructorPhotoRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            'photo' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'photo.required' => 'الصورة مطلوبة',
            'photo.image' => 'يجب أن تكون ملف صورة',
            'photo.mimes' => 'الصيغة غير مدعومة',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 2MB',
        ];
    }
}
