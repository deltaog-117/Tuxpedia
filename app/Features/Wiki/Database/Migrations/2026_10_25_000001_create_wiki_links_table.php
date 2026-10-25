<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wiki_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('wiki_pages')->cascadeOnDelete();
            // A slug, not a page id: the target may not exist yet (a red link).
            $table->string('target_slug');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['page_id', 'target_slug']);
            $table->index('target_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_links');
    }
};
