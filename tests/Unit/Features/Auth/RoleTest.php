<?php

declare(strict_types=1);

namespace Tests\Unit\Features\Auth;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Role;
use Tests\TestCase;

class RoleTest extends TestCase
{
    public function test_every_role_includes_itself_and_the_ones_below_it(): void
    {
        $order = [Role::Reader, Role::Editor, Role::Admin];

        foreach ($order as $i => $role) {
            foreach ($order as $j => $minimum) {
                $this->assertSame($i >= $j, $role->atLeast($minimum), "{$role->value} vs {$minimum->value}");
            }
        }
    }

    public function test_unknown_role_names_are_rejected(): void
    {
        foreach (['', 'root', 'ADMIN', 'moderator'] as $name) {
            $this->assertNull(Role::tryFrom($name), $name);
        }
    }

    public function test_new_accounts_are_readers_and_the_role_is_not_mass_assignable(): void
    {
        $user = new User(['name' => 'a', 'email' => 'a@b.c', 'role' => 'admin']);

        $this->assertSame(Role::Reader, $user->role);
    }

    public function test_a_stored_role_name_is_cast_to_the_enum(): void
    {
        $user = new User();
        $user->forceFill(['role' => 'editor']);

        $this->assertSame(Role::Editor, $user->role);
        $this->assertTrue($user->hasRole(Role::Editor));
        $this->assertFalse($user->hasRole(Role::Admin));
    }
}
