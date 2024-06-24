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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->uuid('session_id');
            $table->string('name');
            $table->bigInteger('phonebook_id');
            $table->string('message_type');
            $table->longText('message');
            $table->enum('status', ['paused', 'completed', 'waiting', 'processing']);
            $table->integer('delay')->default(0);
            $table->timestamp('scheduled_at', 0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
