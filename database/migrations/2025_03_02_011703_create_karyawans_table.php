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
        Schema::create('karyawans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik_karyawan', 255);
            $table->string('nama_karyawan', 255);
            $table->string('foto_karyawan', 255)->nullable();
            $table->enum('statuskaryawan', ['Bulanan', 'Harian', 'Borongan']);
            $table->uuid('id_departemen')->nullable();
            $table->foreign('id_departemen')->references('id')->on('departemens')->onDelete('set null');
            $table->uuid('id_bagian')->nullable();
            $table->foreign('id_bagian')->references('id')->on('bagians')->onDelete('set null');
            $table->date('tgl_awalmmasuk');
            $table->date('tahun_keluar')->nullable();
            $table->uuid('id_jabatan')->nullable();
            $table->foreign('id_jabatan')->references('id')->on('jabatans')->onDelete('set null');
            $table->uuid('id_profesi')->nullable();
            $table->foreign('id_profesi')->references('id')->on('profesis')->onDelete('set null');
            $table->string('nik', 16);
            $table->string('kk', 16);
            $table->enum('statuskawin', ['Lajang', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->enum('pendidikan_terakhir', ['SD/MI', 'SMP/MTS', 'SMA/SMK/MA', 'S1', 'S2', 'S3', 'Lainnya']);
            $table->uuid('id_programstudi')->nullable();
            $table->foreign('id_programstudi')->references('id')->on('program_studis')->onDelete('set null');
            $table->string('no_hp', 16);
            $table->string('ortu_bapak', 255);
            $table->string('ortu_ibu', 255);
            $table->enum('ukuran_kemeja', ['S', 'M', 'L', 'XL', 'XXL', 'XXXL']);
            $table->string('ukuran_celana', 5);
            $table->string('ukuran_sepatu', 5);
            $table->string('jml_anggotakk', 5);
            $table->string('upload_ktp', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
