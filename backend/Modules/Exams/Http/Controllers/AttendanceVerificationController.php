<?php // Modules/Exams/Http/Controllers/AttendanceVerificationController.php

namespace Modules\Exams\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Exams\Filters\BatchAttendanceVerificationFilter;

class AttendanceVerificationController extends Controller
{
    // ... توثيق Swagger (بدون تغيير) ...


    public function index(Request $request)
    {
        try {
            $filter = BatchAttendanceVerificationFilter::fromRequest($request);
            $perPage = min($request->input('per_page', 50), 100);
            $page = max(1, $request->input('page', 1));

            // 1. بناء الاستعلام الرئيسي
            $query = DB::table('batch_student as bst')
                ->select([
                    'students.id as student_id',
                    'students.first_name',
                    'students.last_name',
                    'subjects.id as subject_id',
                    'subjects.name as subject_name',
                    'exams.id as exam_id',
                    DB::raw('DATE_FORMAT(exams.exam_date, "%Y-%m-%d") as exam_date'),
                    'exams.exam_time',
                    'exam_types.name as exam_type',
                    'institute_branches.name as institute_branch',
                    'batches.id as batch_id',
                    DB::raw('DATE_FORMAT(exams.exam_date, "%Y-%m-%d") as exam_date_key'),
                    'batch_subjects.id as batch_subject_id'
                ])
                ->join('students', 'bst.student_id', '=', 'students.id')
                ->join('batches', 'bst.batch_id', '=', 'batches.id')
                ->join('batch_subjects', function ($join) {
                    $join->on('batches.id', '=', 'batch_subjects.batch_id')
                        ->where('batch_subjects.is_active', true);
                })
                ->join('subjects', 'batch_subjects.subject_id', '=', 'subjects.id')
                ->join('exams', 'batch_subjects.id', '=', 'exams.batch_subject_id')
                ->join('exam_types', 'exams.exam_type_id', '=', 'exam_types.id')
                ->join('institute_branches', 'batches.institute_branch_id', '=', 'institute_branches.id');

            // 2. تطبيق الفلاتر
            if ($filter->studentId) {
                $query->where('students.id', $filter->studentId);
            }
            if ($filter->batchId) {
                $query->where('batches.id', $filter->batchId);
            }
            if ($filter->subjectId) {
                $query->where('subjects.id', $filter->subjectId);
            }
            if ($filter->instituteBranchId) {
                $query->where('batches.institute_branch_id', $filter->instituteBranchId);
            }

            $rawData = $query->limit(5000)->get();

            if ($rawData->isEmpty()) {
                return response()->json([
                    'current_page' => $page,
                    'data' => [],
                    'total' => 0,
                    'per_page' => $perPage,
                    'last_page' => 1
                ]);
            }

            // 3. فك تشفير أسماء الطلاب
            $studentIds = $rawData->pluck('student_id')->unique()->toArray();
            $studentsWithNames = $this->getDecryptedStudentNames($studentIds);

            // 4. استخراج المعرفات
            $examIds = $rawData->pluck('exam_id')->unique()->toArray();
            $examDates = $rawData->pluck('exam_date_key')->unique()->toArray();
            $batchIds = $rawData->pluck('batch_id')->unique()->toArray();
            $subjectIds = $rawData->pluck('subject_id')->unique()->toArray();
            $batchSubjectIds = $rawData->pluck('batch_subject_id')->unique()->toArray();

            // 5. جلب بيانات النتائج (كما هو صحيح)
            $examResults = DB::table('exam_results')
                ->select('exam_id', 'student_id')
                ->whereIn('exam_id', $examIds)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->groupBy('exam_id')
                ->mapWithKeys(fn($group, $examId) => [
                    $examId => $group->pluck('student_id')->unique()->toArray()
                ]);

            // 6. ✅ الإصلاح النهائي لاستعلام الحضور (بدون أخطاء)
            $attendances = DB::table('attendances as a')
                ->select(
                    'a.student_id',
                    'a.batch_id',
                    'a.attendance_date',
                    'bsubj.subject_id' // ✅ نحصل على subject_id من الجدول الصحيح
                )
                ->join('batches as b', 'a.batch_id', '=', 'b.id') // ✅ الحضور → الدورات
                ->join('batch_subjects as bsubj', function ($join) use ($batchSubjectIds) {
                    $join->on('b.id', '=', 'bsubj.batch_id') // ✅ الدورات → مواد الدورة
                        ->whereIn('bsubj.id', $batchSubjectIds); // ✅ تصفية المواد
                })
                ->whereIn('a.student_id', $studentIds)
                ->whereIn('a.batch_id', $batchIds)
                ->whereIn('a.attendance_date', $examDates)
                ->where('a.status', 'present')
                ->get()
                ->mapToGroups(function ($item) {
                    $dateKey = $item->attendance_date instanceof \DateTimeInterface
                        ? $item->attendance_date->format('Y-m-d')
                        : substr($item->attendance_date, 0, 10);

                    // ✅ المفتاح الصحيح مع المادة
                    return ["{$item->student_id}|{$item->batch_id}|{$dateKey}|{$item->subject_id}" => true];
                });

            // 7. بناء السجلات
            $records = $rawData->map(function ($item) use ($examResults, $attendances, $studentsWithNames) {
                $dateKey = $item->exam_date_key instanceof \DateTimeInterface
                    ? $item->exam_date_key->format('Y-m-d')
                    : substr($item->exam_date_key, 0, 10);

                $attendanceKey = "{$item->student_id}|{$item->batch_id}|{$dateKey}|{$item->subject_id}";

                $hasResult = isset($examResults[$item->exam_id]) &&
                    in_array($item->student_id, $examResults[$item->exam_id]);

                $attended = $attendances->has($attendanceKey);

                $status = $hasResult ? 'حاضر' : ($attended ? 'لم يقدم' : 'غائب');

                $studentNames = $studentsWithNames[$item->student_id] ?? [
                    'first_name' => 'غير',
                    'last_name' => 'معروف'
                ];

                return [
                    'exam_id' => $item->exam_id,
                    'student_id' => $item->student_id,
                    'student_name' => trim("{$studentNames['first_name']} {$studentNames['last_name']}"),
                    'subject_name' => $item->subject_name,
                    'exam_date' => $item->exam_date,
                    'exam_time' => $item->exam_time,
                    'exam_type' => $item->exam_type,
                    'institute_branch' => $item->institute_branch,
                    'status' => $status,
                ];
            })->values();

            // 8. pagination
            $total = $records->count();
            $offset = ($page - 1) * $perPage;
            $paginatedData = $records->slice($offset, $perPage)->values();

            return response()->json([
                'current_page' => $page,
                'data' => $paginatedData,
                'total' => $total,
                'per_page' => $perPage,
                'last_page' => max(1, ceil($total / $perPage)),
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance Verification Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة لاحقًا.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * الحصول على أسماء الطلاب مع فك التشفير الآمن
     */
    private function getDecryptedStudentNames(array $studentIds): array
    {
        // 1. جلب الأسماء المشفرة مباشرة من قاعدة البيانات
        $encryptedNames = DB::table('students')
            ->whereIn('id', $studentIds)
            ->select('id', 'first_name', 'last_name')
            ->get()
            ->keyBy('id');

        $result = [];

        // 2. فك التشفير لكل طالب مع معالجة الأخطاء
        foreach ($encryptedNames as $student) {
            $firstName = $this->decryptValue($student->first_name, 'first_name', $student->id);
            $lastName = $this->decryptValue($student->last_name, 'last_name', $student->id);

            $result[$student->id] = [
                'first_name' => $firstName ?: 'غير',
                'last_name'  => $lastName  ?: 'معروف'
            ];
        }

        return $result;
    }

    /**
     * فك تشفير قيمة بأمان مع تسجيل الأخطاء
     */
    private function decryptValue(?string $value, string $field, int $studentId): ?string
    {
        if (!$value) return null;

        try {
            // محاولة فك التشفير
            return Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // تسجيل الخطأ دون إيقاف التنفيذ
            Log::warning("Failed to decrypt {$field} for student {$studentId}: {$e->getMessage()}");
            return null;
        } catch (\Exception $e) {
            Log::error("Unexpected error decrypting {$field} for student {$studentId}: {$e->getMessage()}");
            return null;
        }
    }
}
