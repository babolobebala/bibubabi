<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_know_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_know_kategori');
    }
};
