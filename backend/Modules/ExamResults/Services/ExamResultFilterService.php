<?php

namespace Modules\ExamResults\Services;

use Modules\ExamResults\Models\ExamResult;

class ExamResultFilterService
{
    /**
     * يطبّق الفلاتر على نتائج الامتحانات ويعيدها
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function filter(array $filters)
    {
        $query = ExamResult::query()
            ->with([
                'exam',
                'student',
                'exam.batchSubject',   // لو تحتاج المادة
            ]);

        // 🔹 طالب معيّن (إجباري في الـ Request)
        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        // 🔹 فلترة بتاريخ محدد
        if (!empty($filters['date'])) {
            $query->whereHas('exam', function ($q) use ($filters) {
                $q->whereDate('exam_date', $filters['date']);
            });
        } else {
            // 🔹 فلترة بين تاريخين
            if (!empty($filters['date_from'])) {
                $query->whereHas('exam', function ($q) use ($filters) {
                    $q->whereDate('exam_date', '>=', $filters['date_from']);
                });
            }

            if (!empty($filters['date_to'])) {
                $query->whereHas('exam', function ($q) use ($filters) {
                    $q->whereDate('exam_date', '<=', $filters['date_to']);
                });
            }
        }

        // 🔹 فلترة بالمادة
        // - لو عندك subject_id في جدول batch_subjects:
        //   batch_subjects.subject_id
        if (!empty($filters['subject_id'])) {
            $query->whereHas('exam.batchSubject', function ($q) use ($filters) {
                $q->where('subject_id', $filters['subject_id']);
            });
        }

        // 🔹 فلترة بالعلامات (بين قيمتين)
        if (!empty($filters['marks_from'])) {
            $query->where('obtained_marks', '>=', $filters['marks_from']);
        }

        if (!empty($filters['marks_to'])) {
            $query->where('obtained_marks', '<=', $filters['marks_to']);
        }

        // 🔹 ناجح / راسب (اختياري)
        // يدعم: true / false / "true" / "false" / 1 / 0 / "1" / "0"
        if (array_key_exists('is_passed', $filters) && $filters['is_passed'] !== null && $filters['is_passed'] !== '') {

            $raw = $filters['is_passed'];

            // إن كانت أصلاً بوليانية أو رقمية (من الفاليديشن مثلاً)
            if (is_bool($raw) || is_int($raw)) {
                $normalized = (int) $raw; // true => 1, false => 0
            } else {
                // يحوّل "true"/"false"/"1"/"0" إلى true/false
                $bool = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if (!is_null($bool)) {
                    $normalized = $bool ? 1 : 0;
                } else {
                    // لو وصلته قيمة غريبة نتجاهل الفلتر
                    $normalized = null;
                }
            }

            if (!is_null($normalized)) {
                $query->where('is_passed', $normalized);
            }
        }

        // يمكنك إضافة ترتيب معيّن (مثلاً الأحدث أولاً)
        return $query->orderByDesc('id')->get();
    }
}
