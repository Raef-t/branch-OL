<?php

namespace Modules\Shared\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
/**
 * Trait HandlesFileCleanup
 *
 * يوفر طرقاً لحذف الملفات المرتبطة بنماذج Eloquent عند التحديث أو الحذف النهائي.
 *
 * يعتمد على إعدادات config('files.fileFieldsMap') لتحديد الحقول التي تحتوي على مسارات الملفات.
 *
 * @package App\Traits
 */

trait HandlesFileCleanup
{
    /**
     * حذف الملفات القديمة عند تحديث الحقول المرتبطة بالملفات في النموذج.
     *
     * @param \Illuminate\Database\Eloquent\Model $model النموذج الذي تم تحديثه.
     * @return void
     */
    public function deleteOldFiles($model): void
    {
    $fields = config('files.fileFieldsMap')[$model::class] ?? [];

    foreach ($fields as $field) {
        // تحقق إن الحقل موجود فعلاً في الموديل
        if (!array_key_exists($field, $model->getAttributes())) {
            Log::warning("deleteOldFiles: field not present on model", [
                'model' => get_class($model),
                'field' => $field,
            ]);
            continue;
        }

        if ($model->isDirty($field)) {
            $oldPath = $model->getOriginal($field);

            Log::info("deleteOldFiles: field changed", [
                'model'    => get_class($model),
                'id'       => $model->getKey(),
                'field'    => $field,
                'oldValue' => $oldPath,
                'newValue' => $model->{$field},
            ]);

            if (! $oldPath) {
                Log::info("deleteOldFiles: old path empty, nothing to delete", [
                    'model' => get_class($model),
                    'field' => $field,
                ]);
                continue;
            }

            try {
                $deleted = false;

                // 1) إذا المسار يبدو مساراً نسبياً (مثل students/photos/x.jpg) - نحاول مع disk('public')
                if (! Str::startsWith($oldPath, ['http://', 'https://', '/', 'C:\\', 'D:\\']) 
                    && ! Str::startsWith($oldPath, 'storage/')) {
                    if (Storage::disk('public')->exists($oldPath)) {
                        $deleted = Storage::disk('public')->delete($oldPath);
                        Log::info("deleteOldFiles: deleted via Storage::disk('public')", [
                            'path'    => $oldPath,
                            'deleted' => $deleted,
                        ]);
                    } else {
                        Log::info("deleteOldFiles: not found on disk('public')", ['path' => $oldPath]);
                    }
                }

                // 2) إذا المسار يبدأ بـ 'storage/' أو كان URL، حاول حذف عبر public_path('storage/...')
                // استخرج اسم الملف النسبي بعد 'storage/' إن وجد
                $publicRelative = null;
                if (Str::startsWith($oldPath, 'storage/')) {
                    $publicRelative = Str::after($oldPath, 'storage/');
                } elseif (Str::startsWith($oldPath, ['http://', 'https://'])) {
                    // إذا كان URL كامل، حاول استخراج الجزء بعد '/storage/'
                    if (Str::contains($oldPath, '/storage/')) {
                        $publicRelative = Str::after($oldPath, '/storage/');
                    } else {
                        // قد يكون رابط مباشر إلى ملف داخل public, حاول basename كحل أخير
                        $publicRelative = null;
                    }
                } elseif (Str::startsWith($oldPath, ['/'])) {
                    // absolute path - حاول تحويله إلى مسار داخل public إذا ينتمي إليه
                    $publicPath = public_path();
                    if (Str::startsWith($oldPath, $publicPath)) {
                        $publicRelative = Str::after($oldPath, $publicPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR);
                    }
                }

                if (! $deleted && $publicRelative) {
                    $fullPublicPath = public_path('storage/' . $publicRelative);
                    if (file_exists($fullPublicPath)) {
                        $deleted = @unlink($fullPublicPath);
                        Log::info("deleteOldFiles: deleted via public/storage path", [
                            'full_path' => $fullPublicPath,
                            'deleted'   => $deleted,
                        ]);
                    } else {
                        Log::info("deleteOldFiles: not found at public/storage path", [
                            'expected' => $fullPublicPath
                        ]);
                    }
                }

                // 3) آخر محاولة: إذا oldPath هو مسار فعلي كامل (absolute path)
                if (! $deleted && (Str::startsWith($oldPath, ['C:\\', 'D:\\', '/']))) {
                    if (file_exists($oldPath)) {
                        $deleted = @unlink($oldPath);
                        Log::info("deleteOldFiles: deleted via absolute path", [
                            'path'    => $oldPath,
                            'deleted' => $deleted,
                        ]);
                    } else {
                        Log::info("deleteOldFiles: absolute path not found", ['path' => $oldPath]);
                    }
                }

                if (! $deleted) {
                    Log::warning("deleteOldFiles: failed to delete old file (not found or delete returned false)", [
                        'model' => get_class($model),
                        'id'    => $model->getKey(),
                        'field' => $field,
                        'oldValue' => $oldPath,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("deleteOldFiles: exception while deleting old file", [
                    'model' => get_class($model),
                    'id'    => $model->getKey(),
                    'field' => $field,
                    'oldValue' => $oldPath,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
    }

    /**
     * حذف جميع الملفات المرتبطة بالنموذج عند الحذف النهائي.
     *
     * @param \Illuminate\Database\Eloquent\Model $model النموذج الذي سيتم حذفه نهائياً.
     * @return void
     */
    public function deleteAllFiles($model): void
    {
        $fields = config('files.fileFieldsMap')[$model::class] ?? [];

        foreach ($fields as $field) {
            $path = $model->{$field};
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
