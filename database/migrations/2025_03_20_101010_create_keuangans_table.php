<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keuangans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_keuangan', 20)->unique();
            $table->unsignedBigInteger('id_periode');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('verifikator_id')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();

            // Foreign key to periode_gajis table
            $table->foreign('id_periode')->references('id')->on('periodegajis');

            // Foreign key to users table
            $table->foreign('verifikator_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};
