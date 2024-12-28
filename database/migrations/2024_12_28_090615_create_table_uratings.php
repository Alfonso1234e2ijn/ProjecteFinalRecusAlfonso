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
        Schema::create('uratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Usuari calificat
            $table->unsignedBigInteger('rater_id'); // Usuari que dona la valoració
            $table->tinyInteger('rating')->unsigned()->comment('Rating del 1 al 5'); // Rating del 1 al 5
            $table->timestamps();

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rater_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uratings');
    }
};
