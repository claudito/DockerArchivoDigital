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
        Schema::create('API_Seguimiento_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('message');
            $table->text('log');
            $table->unsignedBigInteger('API_Seguimiento_id');
            $table->foreign('API_Seguimiento_id')->references('id')->on('API_Seguimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('API_Seguimiento_logs');
    }
};
