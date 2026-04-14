<?php

namespace Modules\KnowledgeSources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\KnowledgeSources\Database\Factories\KnowledgeSourceFactory;

class KnowledgeSource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

         'name',
        'description',
        'is_active',
    ];

    // protected static function newFactory(): KnowledgeSourceFactory
    // {
    //     // return KnowledgeSourceFactory::new();
    // }
}
