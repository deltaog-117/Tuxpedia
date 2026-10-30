<?php

declare(strict_types=1);

namespace App\Features\Auth\Models;

use App\Features\Auth\ValueObjects\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const BIO_MAX_LENGTH = 280;

    protected $fillable = [
        'name',
        'username',
        'bio',
        'email',
        'password',
        'last_visited_at',
    ];

    // The role is deliberately not fillable; see PromoteUser.
    protected $attributes = [
        'role' => 'reader',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_visited_at' => 'datetime',
            'role' => Role::class,
        ];
    }

    public function hasRole(Role $minimum): bool
    {
        return $this->role->atLeast($minimum);
    }

    /**
     * Up to two capital letters taken from the display name, for the avatar.
     */
    public function initials(): string
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', $this->name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = array_map(fn (string $word): string => mb_substr($word, 0, 1), array_slice($words, 0, 2));

        return $letters === [] ? '?' : mb_strtoupper(implode('', $letters));
    }

    /**
     * A stable colour angle (0-359) per handle, so the same person always gets the same avatar.
     */
    public function avatarHue(): int
    {
        return crc32($this->username) % 360;
    }
}
