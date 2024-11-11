<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperTag
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
    ];

    // Relationships
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tags')
            ->withTimestamps();
    }
}
