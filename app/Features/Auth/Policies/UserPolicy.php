<?php

declare(strict_types=1);

namespace App\Features\Auth\Policies;

use App\Features\Auth\Models\User;

final class UserPolicy
{
    /** Only the owner edits a profile; admins change roles from the command line, not here. */
    public function update(User $actor, User $profile): bool
    {
        return $actor->is($profile);
    }
}
