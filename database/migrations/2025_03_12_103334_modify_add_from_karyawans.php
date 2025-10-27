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
        Schema::table('karyawans', function (Blueprint $table) {
            //
            $table->string('nama_bank', 100)->nullable()->after('upload_ktp');
            $table->string('nomor_rekening', 50)->nullable()->after('nama_bank');
            $table->string('nama_pemilik_rekening', 255)->nullable()->after('nomor_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            //
            $table->dropColumn(['nama_bank', 'nomor_rekening', 'nama_pemilik_rekening']);
        });
    }
};
