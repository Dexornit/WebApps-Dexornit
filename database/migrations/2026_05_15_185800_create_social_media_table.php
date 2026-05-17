<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('social_media');
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->unique(); // whatsapp, instagram, dll
            $table->string('url')->nullable();    // link ke profil/nomor
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
