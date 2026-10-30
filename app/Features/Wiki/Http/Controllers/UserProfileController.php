<?php

declare(strict_types=1);

namespace App\Features\Wiki\Http\Controllers;

use App\Features\Auth\Models\User;
use App\Features\Wiki\Actions\ListUserContributions;
use Illuminate\View\View;

/**
 * The public profile lives in the Wiki feature because most of it is wiki activity;
 * the owner's edit form is in the Auth feature.
 */
class UserProfileController
{
    public function show(string $username, ListUserContributions $contributions): View
    {
        $user = User::where('username', $username)->firstOrFail();

        return view('features.wiki.profile', ['user' => $user] + $contributions->execute($user));
    }
}
