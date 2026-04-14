<?php

namespace Modules\DoorDevices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DoorDevice extends Model implements Auditable
{
     use HasFactory,AuditableTrait;

    protected $fillable = [
        'device_id',
        'name',
        'location',
        'is_active',
        'last_seen_at',
        'api_key',
    ];
}
