<?php

namespace App\Models;

use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Theme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'settings',
        'layouts',
        'assets',
        'status',
        'is_default'
    ];

    protected $casts = [
        'settings' => 'array',
        'layouts' => 'array',
        'assets' => 'array',
        'status' => StatusGlobal::class,
        'is_default' => 'boolean'
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
