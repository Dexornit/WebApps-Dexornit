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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->nullable(); // For emoji or icon class
            $table->string('color')->default('#6C63FF'); // Hex color for UI
            $table->boolean('status')->default(true);
            $table->integer('order')->default(0); // For sorting
            $table->timestamps();
        });
        
        // Insert default categories
        DB::table('categories')->insert([
            [
                'name' => 'Streaming',
                'slug' => 'streaming',
                'icon' => '🎬',
                'color' => '#A8D5FF',
                'status' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tools',
                'slug' => 'tools',
                'icon' => '🛠️',
                'color' => '#D4B5FF',
                'status' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'icon' => '🎮',
                'color' => '#B5FFD4',
                'status' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
