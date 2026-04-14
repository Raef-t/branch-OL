<?php

namespace Modules\FcmTokens\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Users\Models\User;

class FcmToken extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'token',
        'user_id',
        'device_info',
        'last_seen',
    ];

    protected $casts = [
        'device_info' => 'array',  // JSON إلى array تلقائي
        'last_seen' => 'datetime',
    ];

    // Default attributes (هيحط empty array لو NULL)
    protected $attributes = [
        'device_info' => '[]',  // empty array كـ JSON string
    ];

    // علاقة مع اليوزر
    public function user()
    {
        return $this->belongsTo(User::class);  
    }
}