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
        Schema::create('cuti_karyawans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_karyawan')->nullable();
            $table->foreign('id_karyawan')->references('id')->on('karyawans')->onDelete('set null');
            $table->enum('jenis_cuti', ['Izin', 'Cuti']);
            $table->date('tanggal_mulai_cuti');
            $table->date('tanggal_akhir_cuti');
            $table->integer('jumlah_hari_cuti');
            $table->string('cuti_disetujui', 255)->nullable();
            $table->bigInteger('master_cuti_id')->unsigned()->nullable();
            // We'll add the foreign key constraint separately
            $table->string('bukti', 255);
            $table->uuid('id_supervisor')->nullable();
            $table->foreign('id_supervisor')->references('id')->on('karyawans')->onDelete('set null');
            $table->enum('status_acc', ['Menunggu Persetujuan', 'Disetujui', 'Ditolak'])->default('Menunggu Persetujuan');
            $table->text('keterangan_tolak')->nullable();
            $table->timestamp('tanggal_approval')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('karyawans')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti_karyawans');
    }
};
