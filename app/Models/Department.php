<?php

namespace App\Models;

use App\Enums\StatusGlobal;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin IdeHelperDepartment
 */
class Department extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'phone',
        'email',
        'status',
    ];

    protected $casts = [
        'status' => StatusGlobal::class,
    ];

    /**
     * Configuración de ActivityLog
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'code', 'description', 'address', 'phone', 'email', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "Department {$eventName}")
            ->useLogName('department');
    }

    /**
     * Interact with the department's code.
     */
    protected function code(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::upper($value),
            set: fn (string $value) => Str::upper($value),
        );
    }

    /**
     * Interact with the department's name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => $value,
        );
    }

    /**
     * Get formatted phone number.
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => preg_replace('/[^0-9]/', '', $value), // Solo mantiene números
        );
    }

    /**
     * Get formatted email.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value,
            set: fn (?string $value) => $value ? strtolower($value) : null,
        );
    }

    // Relaciones
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
