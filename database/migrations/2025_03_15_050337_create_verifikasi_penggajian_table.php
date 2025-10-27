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
        Schema::create('verifikasi_penggajian', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_penggajian');
            $table->foreign('id_penggajian')->references('id')->on('penggajians')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Menunggu Verifikasi', 'Disetujui', 'Ditolak'])->default('Menunggu Verifikasi');
            $table->text('keterangan')->nullable();
            $table->decimal('total_verifikasi', 15, 2);
            $table->uuid('departemen_id')->nullable();
            $table->foreign('departemen_id')->references('id')->on('departemens')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi_penggajian');
    }
};
