<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    // Relationships
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'uploaded_by');
    }

    /**
     * Get user's reminders.
     */
    public function reminders()
    {
        return $this->hasMany(Reminder::class, 'sent_to');
    }

    /**
     * Get user's unread reminders.
     */
    public function unreadReminders()
    {
        return $this->reminders()->unread();
    }

    /**
     * Get unread reminders count.
     */
    public function getUnreadRemindersCountAttribute()
    {
        return $this->unreadReminders()->count();
    }
}
