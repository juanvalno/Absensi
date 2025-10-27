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
        Schema::table('penggajians', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['Menunggu Verifikasi', 'Disetujui', 'Ditolak'])->default('Menunggu Verifikasi');
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->text('keterangan_verifikasi')->nullable();
            $table->foreignId('verifikasi_oleh')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropForeign(['verifikasi_oleh']);
            $table->dropColumn(['status_verifikasi', 'tanggal_verifikasi', 'keterangan_verifikasi', 'verifikasi_oleh']);
        });
    }
};
