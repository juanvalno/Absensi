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
        Schema::create('jabatans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_jabatan', 255);
            $table->integer('gaji_pokok');
            $table->integer('premi');
            $table->integer('tunjangan_jabatan');
            $table->integer('uang_lembur_biasa');
            $table->integer('uang_lembur_libur');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};