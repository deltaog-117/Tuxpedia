<?php

declare(strict_types=1);

namespace App\Features\Auth\Database\Seeders;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local development account with a throwaway password; run again to reset it.
 */
class MainEditorSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('Refusing to create a known-password account in production.');

            return;
        }

        $user = User::firstOrNew(['email' => 'editor@tuxpedia.local']);
        $user->forceFill([
            'name' => 'editor',
            'username' => 'editor',
            'password' => Hash::make('editor123'),
            'role' => Role::Admin,
        ])->save();
    }
}
