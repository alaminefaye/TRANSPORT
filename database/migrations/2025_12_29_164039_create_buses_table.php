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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('immatriculation')->unique();
            $table->integer('capacity'); // Nombre de sièges
            $table->enum('type', ['VIP', 'Classique', 'Climatisé'])->default('Classique');
            $table->enum('status', ['Disponible', 'En voyage', 'En panne'])->default('Disponible');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
