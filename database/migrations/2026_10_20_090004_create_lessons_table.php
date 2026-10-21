<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->longText('content_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->timestamps();

            $table->unique(['module_id', 'slug']);
            $table->index(['module_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
