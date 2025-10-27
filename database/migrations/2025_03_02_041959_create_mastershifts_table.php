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
        Schema::create('mastercutis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uraian', 255);
            $table->tinyInteger('is_bulanan')->default(0);
            $table->string('cuti_max', 255)->nullable();
            $table->string('izin_max', 255)->nullable();
            $table->tinyInteger('is_potonggaji')->default(0);
            $table->string('nominal', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mastershifts');
    }
};
