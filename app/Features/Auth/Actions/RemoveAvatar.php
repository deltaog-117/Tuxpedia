<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use App\Features\Auth\Models\User;
use Illuminate\Support\Facades\Storage;

final class RemoveAvatar
{
    /**
     * Go back to the initials avatar and delete the stored picture.
     */
    public function execute(User $user): User
    {
        $previous = $user->avatar_path;

        $user->forceFill(['avatar_path' => null])->save();

        if ($previous !== null) {
            Storage::disk('local')->delete($previous);
        }

        return $user;
    }
}
