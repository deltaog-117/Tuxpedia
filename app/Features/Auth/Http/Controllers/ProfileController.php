<?php

declare(strict_types=1);

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\Actions\UpdateProfile;
use App\Features\Auth\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfileController
{
    public function edit(string $username): View
    {
        $user = User::where('username', $username)->firstOrFail();
        Gate::authorize('update', $user);

        return view('features.auth.profile-edit', ['user' => $user]);
    }

    public function update(Request $request, string $username, UpdateProfile $updateProfile): RedirectResponse
    {
        $user = User::where('username', $username)->firstOrFail();
        Gate::authorize('update', $user);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:'.User::BIO_MAX_LENGTH],
        ]);

        $updateProfile->execute($user, $data['name'], $data['bio'] ?? null);

        return redirect()
            ->route('profile.show', $user->username)
            ->with('message', 'Profile updated.');
    }
}
