<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('level')->default('Débutant à Avancé');
            $table->string('duration')->default('2h 30min');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('video_url')->nullable();
            $table->string('duration')->default('15 min');
            $table->integer('order_num')->default(1);
            $table->timestamps();
        });

        Schema::create('chapter_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->boolean('is_completed')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'chapter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapter_progress');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('courses');
    }
};
