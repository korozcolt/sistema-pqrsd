<?php

namespace App\Models;

use App\Enums\ComponentType;
use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'content',
        'settings',
        'display_rules',
        'order',
        'status',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'display_rules' => 'array',
        'type' => ComponentType::class,
        'status' => StatusGlobal::class,
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function displayRules()
    {
        return $this->morphMany(DisplayRule::class, 'ruleable');
    }
}
