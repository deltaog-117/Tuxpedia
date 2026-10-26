<?php

declare(strict_types=1);

namespace App\Features\Wiki\Policies;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Role;
use App\Features\Wiki\Models\Page;

/**
 * Guests never reach these methods: `User` is not nullable, so Laravel denies them first.
 */
final class PagePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(Role::Editor);
    }

    public function update(User $user, Page $page): bool
    {
        if ($page->isProtected()) {
            return $user->hasRole(Role::Admin);
        }

        return $user->hasRole(Role::Editor);
    }

    public function revert(User $user, Page $page): bool
    {
        return $user->hasRole(Role::Admin);
    }

    public function protect(User $user, Page $page): bool
    {
        return $user->hasRole(Role::Admin);
    }
}
