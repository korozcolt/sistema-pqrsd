<?php

namespace App\Models;

use App\Enums\MenuLocation;
use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'location', 'settings', 'status'
    ];

    protected $casts = [
        'settings' => 'json',
        'location' => MenuLocation::class,
        'status' => StatusGlobal::class
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }
}
