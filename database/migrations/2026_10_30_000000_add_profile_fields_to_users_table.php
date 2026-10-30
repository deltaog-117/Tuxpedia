<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->string('bio', 280)->nullable()->after('email');
        });

        $this->backfillUsernames();

        // Every account has a handle now, so the column can refuse a missing one.
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'bio']);
        });
    }

    /**
     * Derive a handle from each display name; a clash or a name with no usable letters falls back to the id.
     */
    private function backfillUsernames(): void
    {
        $taken = [];

        foreach (DB::table('users')->orderBy('id')->get(['id', 'name']) as $user) {
            // 22 characters leave room for "-" and a 7-digit id inside the 30-character limit.
            $base = trim(substr(Str::slug($user->name), 0, 22), '-');
            $handle = strlen($base) >= 3 ? $base : 'user-'.$user->id;

            if (isset($taken[$handle])) {
                $handle = $base !== '' ? $base.'-'.$user->id : 'user-'.$user->id;
            }

            // An id-based handle can still clash with a name that slugs to the same text.
            for ($suffix = 2; isset($taken[$handle]); $suffix++) {
                $handle = 'user-'.$user->id.'-'.$suffix;
            }

            $taken[$handle] = true;
            DB::table('users')->where('id', $user->id)->update(['username' => $handle]);
        }
    }
};
