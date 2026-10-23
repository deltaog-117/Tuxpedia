<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wiki_pages', function (Blueprint $table) {
            // nullOnDelete: removing a distro must never delete the articles written about it.
            $table->foreignId('distribution_id')
                ->nullable()
                ->after('author')
                ->constrained('distributions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('wiki_pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('distribution_id');
        });
    }
};
