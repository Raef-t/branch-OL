<?php

namespace Modules\Enrollments\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function uploadStudentPhoto(UploadedFile $file): string
    {
        return $file->store('students/photos', 'public');
    }
    public function uploadEmployeePhoto(UploadedFile $file): string
    {
        return $file->store('employees/photos', 'public');
    }

    public function uploadStudentIdCard(UploadedFile $file): string
    {
        return $file->store('students/id_cards', 'public');
    }

    // دالة مساعدة للحصول على الرابط العام
    public function getUrl(string $path): string
    {
        return Storage::url($path);
    }

    // في FileUploadService.php
    public function deleteByUrl(string $url): bool
    {
        // إذا كنت تستخدم Storage::disk('public')، فاستخرج المسار النسبي من الرابط
        $path = str_replace(url('/storage/'), '', $url); // أو حسب هيكل روابطك
        return Storage::disk('public')->delete($path);
    }
}
