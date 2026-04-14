<?php

namespace Modules\Students\Services;

use Modules\Students\Models\Student;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class StudentReportService
{
    /**
     * إنشاء تقرير تسجيل الطالب بصيغة DOCX
     */
    public function generateEnrollmentReport(int $studentId): string
    {
        // جلب الطالب مع العلاقات الضرورية
        $student = Student::with([
            'branch',
            'family.guardians.primaryPhone',
            'latestActiveEnrollmentContract',
            'academicRecords',
        ])->findOrFail($studentId);

        $father = $student->father();
        $mother = $student->mother();

        $contract = $student->latestActiveEnrollmentContract;

        // اختيار سجل التاسع إذا وجد، وإلا آخر سجل أكاديمي
        $record = $student->academicRecords()
            ->where('record_type', 'like', '%تاسع%')
            ->latest('created_at')
            ->first()
            ?? $student->latestAcademicRecord;

        // مسار القالب
        $templatePath = module_path(
            'Students',
            'Resources/templates/enrollment_report.docx'
        );

        $template = new TemplateProcessor($templatePath);

        // تعبئة القالب بالقيم
        $template->setValues($this->buildTemplateData(
            $student,
            $father,
            $mother,
            $contract,
            $record
        ));

        // حفظ التقرير
        $relativeDir = 'reports/students';
        Storage::makeDirectory($relativeDir);

        $path = "{$relativeDir}/student_report_{$student->id}.docx";
        $template->saveAs(storage_path("app/{$path}"));

        return $path;
    }

    /**
     * تجهيز بيانات القالب بشكل آمن ومنظم
     */
    protected function buildTemplateData(
        Student $student,
        $father,
        $mother,
        $contract,
        $record
    ): array {
        return [
            'student_full_name'     => (string) ($student->full_name ?? ''),
            'birth_place'           => (string) ($student->birth_place ?? ''),
            'date_of_birth'         => optional($student->date_of_birth)->format('Y-m-d') ?? '',
            'branch_name'           => (string) ($student->branch?->name ?? ''),
            'student_phone'         => (string) ($father?->primaryPhone?->full_phone_number ?? ''),
            'father_full_name'      => trim(($father?->first_name ?? '') . ' ' . ($father?->last_name ?? '')),
            'father_occupation'     => (string) ($father?->occupation ?? ''),
            'father_phone'          => (string) ($father?->primaryPhone?->full_phone_number ?? ''),
            'mother_full_name'      => trim(($mother?->first_name ?? '') . ' ' . ($mother?->last_name ?? '')),
            'mother_occupation'     => (string) ($mother?->occupation ?? ''),
            'mother_phone'          => (string) ($mother?->primaryPhone?->full_phone_number ?? ''),
            'address'               => (string) ($student->address ?? ''),
            'school'                => (string) ($student->school ?? ''),
            'agreed_amount'         => (string) ($contract?->final_amount_usd ?? ''),
            'first_payment'         => (string) ($contract?->first_payment ?? ($contract?->paymentInstallments()->first()?->planned_amount_usd ?? '')),
            'monthly_payment'       => (string) ($contract?->monthly_payment ?? ($contract?->paymentInstallments()->skip(1)->first()?->planned_amount_usd ?? '')),
            'start_attendance_date' => optional($student?->start_attendance_date)->format('Y-m-d') ?? '',
            'total_score'           => (string) ($record?->total_score ?? ''),
            'health_status'         => (string) ($student->health_status ?? ''),
            'psychological_status'  => (string) ($student->psychological_status ?? ''),
        ];
    }
}
