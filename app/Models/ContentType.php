<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\{ComponentType, ContentStatus, MenuLocation, StatusGlobal};
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'component_type',
        'schema',
        'description',
        'validation_rules',
        'default_settings',
        'status'
    ];

    protected $casts = [
        'schema' => 'array',
        'validation_rules' => 'array',
        'default_settings' => 'array',
        'component_type' => ComponentType::class,
        'status' => StatusGlobal::class
    ];

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
