<?php

namespace App\Models;

use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_id', 'title', 'url', 'page_id', 'parent_id',
        'order', 'attributes', 'status'
    ];

    protected $casts = [
        'attributes' => 'json',
        'status' => StatusGlobal::class
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order');
    }

    public function getFullUrlAttribute()
    {
        if ($this->url) {
            return $this->url;
        }

        if ($this->page_id) {
            return $this->page->getFullUrlAttribute();
        }

        return '#';
    }

    public function shouldDisplay(): bool
    {
        if ($this->status !== StatusGlobal::Active) {
            return false;
        }

        if ($this->page_id && !$this->page->isAccessible()) {
            return false;
        }

        return true;
    }
}
