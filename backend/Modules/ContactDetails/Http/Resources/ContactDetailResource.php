<?php

namespace Modules\ContactDetails\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ContactDetailResource",
 *     type="object",
 *     title="Contact Detail Resource",
 *     description="تمثيل تفاصيل جهة الاتصال",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="guardian_id", type="integer", example=3, nullable=true),
 *     @OA\Property(property="student_id", type="integer", example=15, nullable=true),
 *     @OA\Property(property="family_id", type="integer", example=4, nullable=true),
 *     @OA\Property(property="type", type="string", example="phone"),
 *     @OA\Property(property="value", type="string", example="example@email.com", nullable=true),
    *     @OA\Property(property="country_code", type="string", example="+963", nullable=true),
    *     @OA\Property(property="phone_number", type="string", example="987654321", nullable=true),
    *     @OA\Property(property="full_phone_number", type="string", example="+963987654321", nullable=true),
    *     @OA\Property(property="owner_type", type="string", example="student", nullable=true),
    *     @OA\Property(property="owner_name", type="string", example="أحمد محمد", nullable=true),
    *     @OA\Property(property="supports_call", type="boolean", example=true),
    *     @OA\Property(property="supports_whatsapp", type="boolean", example=true),
    *     @OA\Property(property="supports_sms", type="boolean", example=false),
    *     @OA\Property(property="is_primary", type="boolean", example=true),
    *     @OA\Property(property="is_landline", type="boolean", example=false),
 *     @OA\Property(property="notes", type="string", example="للتواصل السريع", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-13 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-13 12:00:00")
 * )
 */
class ContactDetailResource extends JsonResource
{
    public function toArray($request)
    {
         return [
            'id' => $this->id,
            'guardian_id' => $this->guardian_id,
            'student_id' => $this->student_id,
            'family_id' => $this->family_id,
            'type' => $this->type,
            'value' => $this->value,
            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,
            'full_phone_number' => $this->full_phone_number,
            'owner_type' => $this->owner_type,
            'owner_name' => $this->owner_name,
            'supports_call' => (bool) $this->supports_call,
            'supports_whatsapp' => (bool) $this->supports_whatsapp,
            'supports_sms' => (bool) $this->supports_sms,
            'is_primary' => (bool) $this->is_primary,
            'is_landline' => (bool) $this->is_landline,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
