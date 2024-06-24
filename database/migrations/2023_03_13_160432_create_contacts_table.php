<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**N
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->uuid('session_id');
            $table->bigInteger('label_id');
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('marital_status')->nullable();
            $table->string('religion')->nullable();
            $table->date('birth_date')->nullable()->format('Y-m-d');
            $table->integer('region_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('shop_id')->nullable();
            $table->string('voucher')->nullable();
            $table->date('voucher_valid')->nullable()->format('Y-m-d');
            $table->date('voucher_sent')->nullable()->format('Y-m-d');
            $table->date('registration_date')->nullable()->format('Y');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
