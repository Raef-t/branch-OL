<?php

namespace Modules\Guardians\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuardianDashboardResource extends JsonResource
{
    public function toArray($request)
    {
        // $this هو كائن الـ Family في هذا السياق
        $family = $this;
        $user = $family->user;
        $primaryGuardian = $family->guardians->where('is_primary_contact', true)->first() 
                          ?? $family->guardians->first();

        $students = $family->students;
        
        $totalRequired = 0;
        $totalPaid = 0;

        $childrenData = $students->map(function ($student) use (&$totalRequired, &$totalPaid) {
            $contract = $student->latestActiveEnrollmentContract;
            
            if ($contract) {
                $totalRequired += (float) ($contract->final_amount_usd ?? 0);
                $totalPaid += (float) ($contract->paid_amount_usd ?? 0);
            }

            return [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'photo_url' => $student->profile_photo_url,
                'batch' => $student->latestBatchStudent?->batch?->name ?? '—',
            ];
        });

        $totalRemaining = max($totalRequired - $totalPaid, 0);
        $paymentPercentage = $totalRequired > 0 ? ($totalPaid / $totalRequired) * 100 : 0;

        return [
            'guardian' => [
                'id' => $primaryGuardian?->id,
                'name' => $primaryGuardian ? ($primaryGuardian->first_name . ' ' . $primaryGuardian->last_name) : $user->name,
                'photo_url' => null, // لم تتوفر في النظام حالياً
                'welcome_message' => "أهلاً بكم في معهد العلماء للتعليم",
            ],
            'financial_summary' => [
                'total_required_usd' => round($totalRequired, 2),
                'total_paid_usd' => round($totalPaid, 2),
                'total_remaining_usd' => round($totalRemaining, 2),
                'payment_percentage' => round($paymentPercentage, 2),
            ],
            'children' => $childrenData,
        ];
    }
}
