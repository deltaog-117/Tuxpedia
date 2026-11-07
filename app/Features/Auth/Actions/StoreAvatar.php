<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use App\Features\Auth\Models\User;
use App\Features\Auth\ValueObjects\AvatarImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class StoreAvatar
{
    /**
     * Save the picture on the private disk and point the account at it, replacing any earlier one.
     */
    public function execute(User $user, AvatarImage $image): User
    {
        // A random name makes the file unguessable and gives every upload its own cache key.
        $path = 'avatars/'.$user->id.'-'.Str::random(20).'.'.$image->extension();
        $previous = $user->avatar_path;

        Storage::disk('local')->put($path, $image->bytes());
        $user->forceFill(['avatar_path' => $path])->save();

        if ($previous !== null) {
            Storage::disk('local')->delete($previous);
        }

        return $user;
    }
}
