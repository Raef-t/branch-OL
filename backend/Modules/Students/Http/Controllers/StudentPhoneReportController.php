<?php

namespace Modules\Students\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Students\Models\Student;
use Modules\ContactDetails\Models\ContactDetail;
use Modules\Guardians\Models\Guardian;

class StudentPhoneReportController extends Controller
{
    /**
     * توليد تقرير أرقام هواتف الطلاب مع منطق الفرز الذكي
     */
    public function generate(Request $request)
    {
        $request->validate([
            'institute_branch_id' => 'nullable|exists:institute_branches,id',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
            'student_id' => 'nullable|exists:students,id',
        ]);

        $query = Student::query()
            ->with([
                'family.contactDetails',
                'family.guardians.contactDetails',
                'contactDetails'
            ]);

        // الفلاتر
        if ($request->filled('institute_branch_id')) {
            $query->where('institute_branch_id', $request->institute_branch_id);
        }

        if ($request->filled('batch_ids')) {
            $query->whereHas('batchStudents', function ($q) use ($request) {
                $q->whereIn('batch_id', $request->batch_ids);
            });
        }

        if ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        }

        $students = $query->get();

        $reportData = $students->map(function ($student) {
            $allContacts = collect();

            // 1. جمع كل أرقام الطالب المباشرة
            if ($student->contactDetails) {
                foreach ($student->contactDetails as $cd) {
                    $allContacts->push([
                        'id' => $cd->id,
                        'type' => $cd->type,
                        'value' => $cd->full_phone_number ?? $cd->value,
                        'owner_name' => $cd->owner_name ?? ($student->first_name . ' ' . $student->last_name),
                        'owner_type' => $cd->owner_type ?? 'طالب',
                        'is_primary' => (bool)$cd->is_primary,
                        'supports_sms' => (bool)$cd->supports_sms,
                    ]);
                }
            }

            // 2. جمع كل أرقام العائلة
            if ($student->family && $student->family->contactDetails) {
                foreach ($student->family->contactDetails as $cd) {
                    $allContacts->push([
                        'id' => $cd->id,
                        'type' => $cd->type,
                        'value' => $cd->full_phone_number ?? $cd->value,
                        'owner_name' => $cd->owner_name ?? 'العائلة',
                        'owner_type' => $cd->owner_type ?? 'عائلة',
                        'is_primary' => (bool)$cd->is_primary,
                        'supports_sms' => (bool)$cd->supports_sms,
                    ]);
                }
            }

            // 3. جمع كل أرقام الأوصياء
            if ($student->family && $student->family->guardians) {
                foreach ($student->family->guardians as $guardian) {
                    // أرقام ولي الأمر المباشرة
                    if ($guardian->phone) {
                        $allContacts->push([
                            'id' => 'g-phone-' . $guardian->id,
                            'type' => 'phone',
                            'value' => $guardian->phone,
                            'owner_name' => $guardian->first_name . ' ' . $guardian->last_name,
                            'owner_type' => $guardian->relationship,
                            'is_primary' => (bool)$guardian->is_primary_contact,
                            'supports_sms' => (bool)$guardian->is_primary_contact,
                        ]);
                    }
                    // بيانات الاتصال التفصيلية للوصي
                    if ($guardian->contactDetails) {
                        foreach ($guardian->contactDetails as $cd) {
                            $allContacts->push([
                                'id' => $cd->id,
                                'type' => $cd->type,
                                'value' => $cd->full_phone_number ?? $cd->value,
                                'owner_name' => $cd->owner_name ?? ($guardian->first_name . ' ' . $guardian->last_name),
                                'owner_type' => $cd->owner_type ?? $guardian->relationship,
                                'is_primary' => (bool)$cd->is_primary,
                                'supports_sms' => (bool)$cd->supports_sms,
                            ]);
                        }
                    }
                }
            }

            // --- الفرز الذكي (بناءً على المصفوفات الموحدة) ---
            
            // أ. الرقم الأساسي للرسائل (SMS)
            $primarySms = $allContacts->where('is_primary', true)->first() 
                          ?? $allContacts->where('supports_sms', true)->first();

            // ب. الهاتف الأرضي
            $landline = $allContacts->where('type', 'landline')->first();

            // ج. رقم الأب والأم (من خلال صلة القرابة)
            $fatherContact = $allContacts->filter(fn($c) => in_array($c['owner_type'], ['father', 'أب']))->first();
            $motherContact = $allContacts->filter(fn($c) => in_array($c['owner_type'], ['mother', 'أم']))->first();

            // د. الأرقام الإضافية (استبعاد ما تم استخدامه أعلاه)
            $usedIds = collect([$primarySms, $landline, $fatherContact, $motherContact])
                ->filter()->pluck('id');
            
            $others = $allContacts->filter(fn($c) => !$usedIds->contains($c['id']))->values();

            return [
                'id' => $student->id,
                'employeeName' => $student->first_name,
                'surname' => $student->last_name,
                
                'primary_sms' => $primarySms ? "\u{200E}" . $primarySms['value'] : '—',
                'landline' => $landline ? "\u{200E}" . $landline['value'] : '—',
                'father_phone' => $fatherContact ? "\u{200E}" . $fatherContact['value'] : '—',
                'mother_phone' => $motherContact ? "\u{200E}" . $motherContact['value'] : '—',
                
                'contact1' => $others->get(0) ? "\u{200E}" . $others->get(0)['value'] : '—',
                'contact1_owner' => $this->getFormatOwnerInfo($others->get(0)),
                
                'contact2' => $others->get(1) ? "\u{200E}" . $others->get(1)['value'] : '—',
                'contact2_owner' => $this->getFormatOwnerInfo($others->get(1)),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reportData,
        ]);
    }

    private function getFormatOwnerInfo($contact)
    {
        if (!$contact) return '';
        $name = $contact['owner_name'] ?? '';
        $type = $contact['owner_type'] ?? '';
        
        if (!$name && !$type) return '';
        return "({$name} - {$type})";
    }
}
