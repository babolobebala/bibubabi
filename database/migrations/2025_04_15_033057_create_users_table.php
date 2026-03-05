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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique()->nullable();
            $table->string('nip_baru')->unique()->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('nama')->nullable();
            $table->string('email_bps')->unique()->nullable();
            $table->string('email_gmail')->unique()->nullable();
            $table->enum('status_pegawai', ['aktif', 'tidak aktif'])->default('aktif');
            $table->string('golongan')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('url_foto')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
