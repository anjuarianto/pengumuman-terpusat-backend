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
        Schema::create('pengumuman_to', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengumuman_id');
            $table->boolean('is_single_user');
            $table->unsignedBigInteger('penerima_id');
            $table->timestamps();

            $table->foreign('pengumuman_id')->references('id')->on('pengumuman')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_to');
    }
};
