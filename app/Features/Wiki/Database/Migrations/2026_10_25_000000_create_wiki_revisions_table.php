<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wiki_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('wiki_pages')->cascadeOnDelete();
            // Content lives in the wiki Git repository; the row points at it.
            $table->string('sha', 40);
            // The file's path at that commit: it changes when a page is renamed.
            $table->string('path');
            $table->string('author')->nullable();
            $table->string('summary')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['page_id', 'sha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_revisions');
    }
};
