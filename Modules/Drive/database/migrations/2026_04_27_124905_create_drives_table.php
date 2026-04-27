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
        Schema::create('app_drive_list', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('link')->nullable();
            $table->enum('jenis', ['personal', 'tim']);
            
            $table->unsignedBigInteger('personal')->nullable();
            $table->foreign('personal')->references('id')->on('users')->onDelete('set null');
            
            $table->unsignedBigInteger('tim')->nullable();
            $table->foreign('tim')->references('id')->on('roles')->onDelete('set null');

            $table->enum('akses', ['edit', 'view'])->default('edit');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_drive_list');
    }
};
