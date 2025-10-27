<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('special_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table untuk role
        Schema::create('role_special_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('special_permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'special_permission_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_special_permission');
        Schema::dropIfExists('special_permissions');
    }
};