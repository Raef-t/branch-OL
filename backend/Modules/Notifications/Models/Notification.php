<?php

namespace Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\NotificationAttachments\Models\NotificationAttachment;
use Modules\NotificationRecipients\Models\NotificationRecipient;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Notification extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'title',
        'body',
        'template_id',
        'sender_id',
        'sender_type',
        'target_snapshot',
    ];

    protected $casts = [
        'target_snapshot' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(\Modules\MessageTemplates\Models\MessageTemplate::class, 'template_id');
    }

    public function attachments()
    {
        return $this->hasMany(NotificationAttachment::class);
    }

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }
}
