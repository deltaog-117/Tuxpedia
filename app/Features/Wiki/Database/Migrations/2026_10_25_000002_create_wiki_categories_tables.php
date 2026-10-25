<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wiki_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('wiki_category_page', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained('wiki_categories')->cascadeOnDelete();
            $table->foreignId('page_id')->constrained('wiki_pages')->cascadeOnDelete();

            $table->primary(['category_id', 'page_id']);
            $table->index('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wiki_category_page');
        Schema::dropIfExists('wiki_categories');
    }
};
