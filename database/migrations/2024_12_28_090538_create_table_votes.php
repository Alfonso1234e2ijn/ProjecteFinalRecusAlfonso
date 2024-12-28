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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->boolean('type'); // like or dislike
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('response_id');
            $table->boolean('read')->default(false); // Nueva columna 'read'
            $table->timestamps();

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('response_id')->references('id')->on('responses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
