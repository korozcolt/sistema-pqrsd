<?php

namespace App\Models;

use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisplayRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ruleable_type', 'ruleable_id', 'type',
        'conditions', 'status'
    ];

    protected $casts = [
        'conditions' => 'json',
        'status' => StatusGlobal::class
    ];

    public function ruleable()
    {
        return $this->morphTo();
    }
}
