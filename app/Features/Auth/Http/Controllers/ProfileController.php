<?php

declare(strict_types=1);

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\Actions\RemoveAvatar;
use App\Features\Auth\Actions\StoreAvatar;
use App\Features\Auth\Actions\UpdateProfile;
use App\Features\Auth\Exceptions\InvalidAvatarException;
use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\AvatarImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController
{
    public function edit(string $username): View
    {
        $user = User::where('username', $username)->firstOrFail();
        Gate::authorize('update', $user);

        return view('features.auth.profile-edit', ['user' => $user]);
    }

    public function update(
        Request $request,
        string $username,
        UpdateProfile $updateProfile,
        StoreAvatar $storeAvatar,
        RemoveAvatar $removeAvatar,
    ): RedirectResponse {
        $user = User::where('username', $username)->firstOrFail();
        Gate::authorize('update', $user);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:'.User::BIO_MAX_LENGTH],
            // The real checks are in AvatarImage; this only rejects a failed upload early.
            'avatar' => ['nullable', 'file'],
            'remove_avatar' => ['nullable', 'boolean'],
        ]);

        // Check the picture before saving anything, so a bad one leaves the profile untouched.
        $avatar = $request->file('avatar');

        try {
            $image = $avatar === null ? null : AvatarImage::fromBytes((string) $avatar->getContent());
        } catch (InvalidAvatarException $e) {
            throw ValidationException::withMessages(['avatar' => $e->getMessage()]);
        }

        $updateProfile->execute($user, $data['name'], $data['bio'] ?? null);

        if ($image !== null) {
            $storeAvatar->execute($user, $image);
        } elseif ($request->boolean('remove_avatar')) {
            $removeAvatar->execute($user);
        }

        return redirect()
            ->route('profile.show', $user->username)
            ->with('message', 'Profile updated.');
    }
}
