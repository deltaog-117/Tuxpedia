<?php

declare(strict_types=1);

namespace App\Features\Auth\Console;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class PromoteUser extends Command
{
    protected $signature = 'user:promote {email : Email of the account} {role : reader, editor or admin}';

    protected $description = 'Set the role of an account';

    public function handle(): int
    {
        $role = Role::tryFrom((string) $this->argument('role'));

        if ($role === null) {
            $this->error('Role must be one of: reader, editor, admin.');

            return self::FAILURE;
        }

        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No account with that email.');

            return self::FAILURE;
        }

        // Not mass-assignable: a role can only be set here or in a seeder, never by a form.
        $user->forceFill(['role' => $role])->save();

        Log::info('User role changed', ['user_id' => $user->id, 'role' => $role->value]);
        $this->info("{$user->email} is now {$role->value}.");

        return self::SUCCESS;
    }
}
