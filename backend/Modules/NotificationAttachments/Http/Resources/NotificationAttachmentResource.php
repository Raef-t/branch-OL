<?php

namespace Modules\NotificationAttachments\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'notification_id' => $this->notification_id,

            // معلومات الملف
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url' => $this->file_url,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->formatted_size,
            'file_type' => $this->file_type,
            'mime_type' => $this->mime_type,
            'file_extension' => $this->file_extension,

            // معلومات إضافية
            'title' => $this->title,
            'description' => $this->description,
            'sort_order' => $this->sort_order,

            // خصائص مساعدة
            'is_active' => $this->is_active,
            'is_image' => $this->is_image,
            'is_document' => $this->is_document,
            'download_url' => $this->download_url,

            // تواريخ
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
