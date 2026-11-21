<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tour_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('duration_days')->default(3);
            $table->string('duration_label')->nullable();
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->text('highlights')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
