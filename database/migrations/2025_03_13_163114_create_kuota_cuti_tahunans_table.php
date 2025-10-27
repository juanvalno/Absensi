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
        Schema::create('kuota_cuti_tahunans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('karyawan_id');
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('kuota_awal');
            $table->integer('kuota_digunakan')->default(0);
            $table->integer('kuota_sisa');
            $table->date('tanggal_expired')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Composite unique index to ensure one record per karyawan per year
            $table->unique(['karyawan_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuota_cuti_tahunans');
    }
};