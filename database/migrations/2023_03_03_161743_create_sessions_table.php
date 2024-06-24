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
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_name');
            $table->string('whatsapp_number')->nullable();
            $table->bigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->enum('status', ['CONNECTED', 'STOPPED'])->default('STOPPED');
            $table->text('webhook')->nullable();
            $table->string('api_key')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
