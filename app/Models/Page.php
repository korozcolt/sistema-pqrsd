<?php

namespace App\Models;

use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'meta_keywords',
        'layout',
        'settings',
        'og_data',
        'searchable',
        'status',
        'order',
        'theme_id'
    ];

    protected $casts = [
        'settings' => 'json',
        'og_data' => 'json',
        'searchable' => 'boolean',
        'status' => StatusGlobal::class
    ];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function contents()
    {
        return $this->hasManyThrough(Content::class, Section::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

}
