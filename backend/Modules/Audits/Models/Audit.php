<?php

namespace Modules\Audits\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table;

    /**
     * Cast old_values and new_values to arrays automatically.
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('audit.drivers.database.table', 'audits');
    }

    public function user()
    {
        $morphPrefix = config('audit.user.morph_prefix', 'user');
        return $this->morphTo(__FUNCTION__, "{$morphPrefix}_type", "{$morphPrefix}_id");
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}