<?php

namespace Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => Str::limit($this->body, 100), // مقتطف قصير

            // معلومات المرسل (آمنة)
            'sender' => [
                'type' => $this->sender_type,
                'id' => $this->sender_id,
                'display_name' => $this->getSenderDisplayName(),
            ],

            // القالب المستخدم
            'template' => $this->template ? [
                'id' => $this->template->id,
                'name' => $this->template->name,
            ] : null,

            // 📊 إحصائيات التوزيع (مهم جدًا للإدارة)
            'distribution' => [
                'total_recipients' => $this->recipients_count ?? 0,
                'read_count' => $this->read_count ?? 0,
                'delivered_count' => $this->delivered_count ?? 0,
                'read_percentage' => $this->recipients_count ?
                    round(($this->read_count / $this->recipients_count) * 100, 1) : 0,
                'delivered_percentage' => $this->recipients_count ?
                    round(($this->delivered_count / $this->recipients_count) * 100, 1) : 0,
            ],

            // 📎 معلومات المرفقات
            'attachments' => [
                'count' => $this->attachments->count(),
                'files' => $this->attachments->map(fn($a) => [
                    'id' => $a->id,
                    'name' => $a->file_name,
                    'size_formatted' => $this->formatBytes($a->size),
                ])->values(),
            ],

            // 📅 معلومات الوقت
            'created_at' => $this->created_at->toDateTimeString(),
            'created_at_human' => $this->created_at->diffForHumans(),

            // 🔒 معلومات حساسة (للإدارة فقط)
            'target_snapshot' => $this->target_snapshot, // مسموح للإدارة
            'status' => $this->getStatus(),
        ];
    }

    private function getSenderDisplayName(): string
    {
        $map = [
            'admin' => 'الإدارة',
            'system' => 'النظام',
            'user' => 'مستخدم',
            'teacher' => 'المعلم',
            'employee' => 'الموظف',
        ];
        return $map[$this->sender_type] ?? 'غير معروف';
    }

    private function getStatus(): string
    {
        if ($this->recipients_count == 0) return 'no_recipients';
        if ($this->delivered_count >= $this->recipients_count * 0.9) return 'delivered';
        if ($this->delivered_count < $this->recipients_count * 0.1) return 'pending';
        return 'partial';
    }

    private function formatBytes($bytes, $precision = 1): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
