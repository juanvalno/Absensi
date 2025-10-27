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
        Schema::create('absensis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('karyawan_id', 36)->nullable();
            $table->date('tanggal')->nullable();
            $table->char('jadwalkerja_id', 36)->nullable();
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->string('total_jam', 255)->nullable();
            $table->integer('keterlambatan')->default(0);
            $table->integer('pulang_awal')->default(0);
            $table->enum('status', ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Cuti'])->default('Hadir');
            $table->enum('jenis_absensi_masuk', ['Manual', 'Mesin'])->default('Manual');
            $table->unsignedBigInteger('mesinabsensi_masuk_id')->nullable();
            $table->enum('jenis_absensi_pulang', ['Manual', 'Mesin'])->default('Manual');
            $table->unsignedBigInteger('mesinabsensi_pulang_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints if needed
            $table->foreign('karyawan_id')->references('id')->on('karyawans');
            $table->foreign('jadwalkerja_id')->references('id')->on('jadwalkerjas');
            $table->foreign('mesinabsensi_masuk_id')->references('id')->on('mesinabsensis');
            $table->foreign('mesinabsensi_pulang_id')->references('id')->on('mesinabsensis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};