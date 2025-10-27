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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('karyawan_id', 36)->nullable();
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->char('supervisor_id', 36)->nullable();
            $table->foreign('supervisor_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->char('total_lembur', 255)->nullable();
            $table->char('lembur_disetujui', 255)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->enum('jenis_lembur', ['Hari Kerja', 'Hari Libur']);
            $table->enum('status', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak'])->default('Menunggu Persetujuan');
            $table->text('keterangan_tolak')->nullable();
            $table->timestamp('tanggal_approval')->nullable();
            $table->char('approved_by', 36)->nullable();
            $table->foreign('approved_by')->references('id')->on('karyawans')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};