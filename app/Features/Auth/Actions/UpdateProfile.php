<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use App\Features\Auth\Models\User;

final class UpdateProfile
{
    /**
     * The handle, email and role are deliberately not parameters: a profile form cannot change them.
     */
    public function execute(User $user, string $name, ?string $bio): User
    {
        $bio = $bio === null ? null : trim($bio);

        $user->forceFill([
            'name' => trim($name),
            'bio' => $bio === '' ? null : $bio,
        ])->save();

        return $user;
    }
}
