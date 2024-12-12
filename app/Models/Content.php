<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'content_type_id',
        'section_id',
        'content',
        'display_conditions',
        'settings',
        'start_date',
        'end_date',
        'order',
        'status',
        'published_at',
        'is_active'
    ];

    protected $casts = [
        'content' => 'array',
        'display_conditions' => 'array',
        'settings' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'status' => StatusGlobal::class,
        'is_active' => 'boolean'
    ];

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function displayRules()
    {
        return $this->morphMany(DisplayRule::class, 'ruleable');
    }
}
