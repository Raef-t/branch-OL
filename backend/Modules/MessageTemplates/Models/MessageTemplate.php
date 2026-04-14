<?php

namespace Modules\MessageTemplates\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MessageTemplate extends Model implements Auditable
{
    use HasFactory, AuditableTrait;

    protected $table = 'message_templates';

    protected $fillable = [
        'name',
        'category',
        'type',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
