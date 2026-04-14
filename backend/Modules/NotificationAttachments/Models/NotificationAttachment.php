<?php

namespace Modules\NotificationAttachments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Notifications\Models\Notification;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class NotificationAttachment extends Model implements Auditable 
{
    use HasFactory, AuditableTrait;

    protected $fillable = [
        'notification_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
