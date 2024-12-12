<?php

namespace App\Models;

use App\Enums\SectionType;
use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    protected $fillable = [
        'name',
        'identifier',
        'type',
        'config',
        'settings',
        'slug',
        'position',
        'page_id',
        'order',
        'display_rules',
        'styles',
        'is_global',
        'is_active'
    ];

    protected $casts = [
        'settings' => 'array',
        'config' => 'array',
        'display_rules' => 'array',
        'styles' => 'array',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
        'type' => SectionType::class
    ];

    public function getViewComponent(): string
    {
        return $this->type->getViewComponent();
    }

    public function getContent()
    {
        $relation = $this->type->getContentRelation();
        return $this->$relation;
    }

    // Relaciones existentes mantienen la misma estructura
    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'config->menu_id');
    }

    public function slider()
    {
        return $this->contents()->where('content_type_id', function ($query) {
            $query->select('id')
                ->from('content_types')
                ->where('component_type', 'slider')
                ->first();
        });
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}
