<?php

namespace Modules\AuthorizedDevices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AuthorizedDevice extends Model implements Auditable
{
    use HasFactory,AuditableTrait;

    protected $fillable = [
        'device_id',
        'device_name',
        'is_active',
        'last_used_at',
    ];
}
