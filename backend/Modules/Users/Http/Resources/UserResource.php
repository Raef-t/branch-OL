<?php

namespace Modules\Users\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="UserResource",
     *     title="User Resource",
     *     description="تمثيل بيانات المستخدم الموحدة في النظام",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="unique_id", type="string", example="EMP-0001"),
     *     @OA\Property(property="name", type="string", example="محمد الأحمد"),
     *     @OA\Property(property="type", type="string", example="employee"),
     *     @OA\Property(property="related_id", type="integer", example=15),
     *     @OA\Property(property="photo_url", type="string", example="https://example.com/storage/employees/15.jpg  "),
     *     @OA\Property(property="first_name", type="string", example="محمد"),
     *     @OA\Property(property="last_name", type="string", example="الأحمد"),
     *     @OA\Property(property="full_name", type="string", example="محمد الأحمد"),
     *     @OA\Property(
     *         property="instituteBranch",
     *         type="object",
     *         @OA\Property(property="id", type="integer", example=3),
     *         @OA\Property(property="name", type="string", example="فرع جدة")
     *     ),
     *     @OA\Property(
     *         property="extra",
     *         type="object",
     *         example={"position": "محاسب", "job_type": "دوام كامل"}
     *     ),
     *     @OA\Property(
     *         property="roles",
     *         type="array",
     *         @OA\Items(type="string", example="employee")
     *     ),
     *     @OA\Property(
     *         property="permissions",
     *         type="array",
     *         @OA\Items(type="string", example="view_attendance")
     *     ),
     *     @OA\Property(property="created_at", type="string", example="2025-11-09 11:55:00")
     * )
     */

    public function toArray($request)
    {
        $user = $this->resource;

        // تحديد نوع المستخدم
        $type = $this->safeDetermineUserType($user);

        // جلب بيانات الملف الشخصي
        $profile = $this->safeProfileData($user, $type);

        return [
            'id' => $user->id,
            'unique_id' => $user->unique_id,
            'name' => $user->name,
            'type' => $type,

            // الحقول الجديدة: الاسم الأول والأخير والاسم الكامل
            'first_name' => $profile['first_name'] ?? null,
            'last_name' => $profile['last_name'] ?? null,
            'full_name' => $profile['full_name'] ?? null,

            'related_id' => $profile['related_id'] ?? null,
            'photo_url' => $profile['photo_url'] ?? null,
            'students' => $profile['students'] ?? null,

            'instituteBranch' => $profile['instituteBranch'] ?? null,
            'extra' => $profile['extra'] ?? null,

            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'created_at' => optional($user->created_at)->toDateTimeString(),
        ];
    }

    /**
     * تحديد نوع المستخدم
     */
    protected function safeDetermineUserType($user): ?string
    {
        try {
            if ($user->relationLoaded('employee') && $user->employee) return 'employee';
            if ($user->relationLoaded('teacher') && $user->teacher) return 'teacher';
            if ($user->relationLoaded('student') && $user->student) return 'student';
            if ($user->relationLoaded('family') && $user->family) return 'family';
            if ($user->hasRole('admin')) return 'admin';
        } catch (\Throwable $e) {
            Log::warning('UserResource determine type error: ' . $e->getMessage());
        }
        return null;
    }

    /**
     * جلب بيانات الملف الشخصي بناء على نوع المستخدم
     */
    protected function safeProfileData($user, ?string $type): array
    {
        try {
            return match ($type) {
                'employee' => [
                    'related_id' => $user->employee?->id,
                    'first_name' => $user->employee?->first_name ?? null,
                    'last_name' => $user->employee?->last_name ?? null,
                    'full_name' => $user->employee?->full_name
                        ?? trim("{$user->employee?->first_name} {$user->employee?->last_name}"),
                    'photo_url' => $user->employee?->photo_url,
                    'instituteBranch' => [
                        'id' => $user->employee?->instituteBranch?->id,
                        'name' => $user->employee?->instituteBranch?->name,
                    ],
                    'extra' => [
                        'position' => $user->employee?->job_title,
                        'job_type' => $user->employee?->job_type,
                    ],
                ],

                'teacher' => [
                    'related_id' => $user->teacher?->id,
                    'first_name' => $user->teacher?->first_name ?? null,
                    'last_name' => $user->teacher?->last_name ?? null,
                    'full_name' => $user->teacher?->full_name
                        ?? trim("{$user->teacher?->first_name} {$user->teacher?->last_name}"),
                    'photo_url' => $user->teacher?->photo_url,
                    'instituteBranch' => [
                        'id' => $user->teacher?->instituteBranch?->id,
                        'name' => $user->teacher?->instituteBranch?->name,
                    ],
                    'extra' => [
                        'specialization' => $user->teacher?->specialization,
                    ],
                ],

                'student' => [
                    'related_id' => $user->student?->id,
                    'first_name' => $user->student?->first_name ?? null,
                    'last_name' => $user->student?->last_name ?? null,
                    'full_name' => $user->student?->full_name
                        ?? trim("{$user->student?->first_name} {$user->student?->last_name}"),
                    'photo_url' => $user->student?->profile_photo_url,
                    'instituteBranch' => [
                        'id' => $user->student?->instituteBranch?->id,
                        'name' => $user->student?->instituteBranch?->name,
                    ],
                    'extra' => [
                        'batch' => $user->student?->latestBatchStudent?->batch?->name,
                        'gender' => $user->student?->gender,
                        'date_of_birth' => $user->student?->date_of_birth?->format('Y-m-d'),
                    ],
                ],

                'family' => [
                    'related_id' => $user->family?->id,
                    'first_name' => $user->family?->first_name ?? null,
                    'last_name' => $user->family?->last_name ?? null,
                    'full_name' => $user->family?->full_name
                        ?? trim("{$user->family?->first_name} {$user->family?->last_name}"),
                    'students' => $user->family?->students?->map(function ($student) {
                        return [
                            'id' => $student->id,
                            'unique_id' => $student->user?->unique_id,
                            'name' => $student->full_name ?? trim("{$student->first_name} {$student->last_name}"),
                            'photo_url' => $student->profile_photo_url,
                        ];
                    }),
                    'extra' => [
                        'students_count' => $user->family?->students?->count(),
                        'relationship' => $user->family?->relationship ?? null,
                    ],
                ],

                'admin' => [
                    'related_id' => null,
                    'first_name' => $user->first_name ?? null,
                    'last_name' => $user->last_name ?? null,
                    'full_name' => $user->full_name
                        ?? trim("{$user->first_name} {$user->last_name}")
                        ?? $user->name,
                    'photo_url' => $user->photo_url ?? null,
                    'instituteBranch' => null,
                    'extra' => ['title' => 'مدير النظام'],
                ],

                default => [],
            };
        } catch (\Throwable $e) {
            Log::warning('UserResource profile error: ' . $e->getMessage());
            return [];
        }
    }
}
