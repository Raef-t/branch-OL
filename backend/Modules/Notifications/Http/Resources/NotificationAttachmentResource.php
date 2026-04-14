<?php

namespace Modules\Notifications\Http\Resources;

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
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'file_url' => $this->file_url,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'mime_type' => $this->mime_type,
            'uploaded_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'download_url' => $this->when($this->file_url, function () {
                return url($this->file_url);
            }),
        ];
    }
}
