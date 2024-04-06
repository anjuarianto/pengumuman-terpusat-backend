<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengumuman_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->constrained('pengumuman')->cascadeOnDelete();
            $table->string('file');
            $table->string('original_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_files');
    }
};
