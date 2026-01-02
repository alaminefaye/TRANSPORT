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
        Schema::dropIfExists('seat_segments');
        
        Schema::create('seat_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->integer('seat_number');
            $table->foreignId('from_stop_id')->constrained('stops')->onDelete('cascade');
            $table->foreignId('to_stop_id')->constrained('stops')->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Index pour optimiser les recherches de disponibilité
            $table->index(['trip_id', 'seat_number']);
            $table->index(['trip_id', 'from_stop_id', 'to_stop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_segments');
    }
};
