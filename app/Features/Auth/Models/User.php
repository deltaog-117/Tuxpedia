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

    protected $fillable = [
        'name',
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
}
