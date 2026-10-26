<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wiki_talk_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('wiki_pages')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('body');
            $table->string('author');
            // updated_at is bumped by each reply, so busy topics sort first.
            $table->timestamps();

            $table->index(['page_id', 'updated_at']);
        });

        Schema::create('wiki_talk_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('wiki_talk_topics')->cascadeOnDelete();
            $table->text('body');
            $table->string('author');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_talk_replies');
        Schema::dropIfExists('wiki_talk_topics');
    }
};
