<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->uuid('id_keuangan')->nullable();
            $table->foreign('id_keuangan')->references('id')->on('keuangans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('penggajians', function (Blueprint $table) {
            $table->dropForeign(['id_keuangan']);
            $table->dropColumn('id_keuangan');
        });
    }
};
