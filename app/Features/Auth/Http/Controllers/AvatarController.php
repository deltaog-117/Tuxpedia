<?php

declare(strict_types=1);

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AvatarController
{
    /**
     * Serve a profile picture from the private disk.
     *
     * The type comes from the stored extension, never from the bytes, and the response
     * forbids sniffing and scripting, so a bad file can only ever be shown as an image.
     */
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();
        $path = $user->avatar_path;
        $disk = Storage::disk('local');

        abort_if($path === null || ! $disk->exists($path), 404);

        return response((string) $disk->get($path), 200, [
            'Content-Type' => str_ends_with($path, '.webp') ? 'image/webp' : 'image/jpeg',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            // The URL carries a version (see User::avatarUrl), so a new upload gets a new URL.
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
