<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['wiki_pages', 'wiki_revisions', 'wiki_talk_topics', 'wiki_talk_replies'];

    public function up(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                // Null for rows written before accounts were linked; the author text stays as the fallback.
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        $this->backfillFromNames();
    }

    public function down(): void
    {
        foreach (self::TABLES as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }

    /**
     * Link old rows to an account only when exactly one account has that display name;
     * a shared name could belong to either person, so those rows stay unlinked.
     */
    private function backfillFromNames(): void
    {
        $owners = DB::table('users')
            ->select('name', DB::raw('MIN(id) as id'))
            ->groupBy('name')
            ->havingRaw('COUNT(*) = 1')
            ->get();

        foreach ($owners as $owner) {
            foreach (self::TABLES as $name) {
                DB::table($name)->whereNull('user_id')->where('author', $owner->name)->update(['user_id' => $owner->id]);
            }
        }
    }
};
