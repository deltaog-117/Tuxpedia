<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Username;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function execute(string $name, Username $username, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'username' => $username->toString(),
            'email' => $email,
            'password' => Hash::make($password),
            'last_visited_at' => now(),
        ]);

        return $user;
    }
}
