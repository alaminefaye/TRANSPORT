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
        Schema::create('loyalty_point_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->onDelete('set null');
            $table->foreignId('from_stop_id')->constrained('stops')->onDelete('cascade');
            $table->date('earned_date'); // Date à laquelle le point a été gagné
            $table->integer('points')->default(1); // Nombre de points gagnés (généralement 1)
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['client_id', 'earned_date']);
            $table->index(['client_id', 'earned_date', 'from_stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_earnings');
    }
};
