<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Normaliza el email eliminando acentos y caracteres especiales.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                // Convertir a minúsculas
                $value = strtolower($value);

                // Remover tildes y caracteres especiales
                $value = $this->removeAccents($value);

                return $value;
            },
        );
    }

    /**
     * Remueve acentos y caracteres especiales de un string.
     *
     * @param string $string
     * @return string
     */
    private function removeAccents(string $string): string
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Decomposición para caracteres latinos
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ý' => 'y',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ý' => 'Y',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u', 'ÿ' => 'y',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U', 'Ÿ' => 'Y',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            'ç' => 'c', 'Ç' => 'C',
            // Más caracteres especiales que pudieran aparecer en nombres o dominios
            'ã' => 'a', 'õ' => 'o', 'Ã' => 'A', 'Õ' => 'O',
            'ø' => 'o', 'Ø' => 'O',
            'æ' => 'ae', 'Æ' => 'AE',
            'œ' => 'oe', 'Œ' => 'OE',
            'ð' => 'd', 'Ð' => 'D',
            'þ' => 'th', 'Þ' => 'TH',
        ];

        // Primero intentamos con strtr() que es más rápido
        $string = strtr($string, $chars);

        // Si aún quedan caracteres especiales, usamos transliterator
        if (preg_match('/[\x80-\xff]/', $string) && function_exists('transliterator_transliterate')) {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
        }

        return $string;
    }
}
