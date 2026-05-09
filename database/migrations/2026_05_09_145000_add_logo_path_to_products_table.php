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
        Schema::table('products', function (Blueprint $table) {
            // Add logo_path column
            $table->string('logo_path')->nullable()->after('name');
            
            // Change category from enum to foreign key
            $table->dropColumn('category');
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('logo_path')->constrained('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['logo_path', 'category_id']);
            $table->enum('category', ['streaming', 'tools', 'gaming'])->after('emoji');
        });
    }
};
