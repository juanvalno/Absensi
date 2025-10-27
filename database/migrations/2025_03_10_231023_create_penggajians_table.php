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
        Schema::create('penggajians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('id_periode');
            $table->char('id_karyawan', 36);
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('tunjangan', 15, 2)->default(0.00)->nullable();
            $table->longText('detail_tunjangan')->nullable();
            $table->decimal('potongan', 15, 2)->default(0.00)->nullable();
            $table->longText('detail_potongan')->nullable();
            $table->longText('detail_departemen')->nullable();
            $table->decimal('gaji_bersih', 15, 2);
            $table->timestamps();

            // Foreign key to periode_gajis table
            $table->foreign('id_periode')->references('id')->on('periodegajis');

            // Foreign key to karyawans table
            $table->foreign('id_karyawan')->references('id')->on('karyawans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajians');
    }
};
