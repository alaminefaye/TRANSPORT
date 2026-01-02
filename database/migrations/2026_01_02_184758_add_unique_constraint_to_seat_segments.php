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
        Schema::table('seat_segments', function (Blueprint $table) {
            // Ajouter un index composite pour améliorer les performances des vérifications
            // Note: On ne peut pas créer une contrainte unique car les segments peuvent se chevaucher
            // si les trajets sont différents (ex: siège 1 de A->B et siège 1 de C->D si C est après B)
            // La logique de vérification des chevauchements est gérée dans le code avec des verrous
            $table->index(['trip_id', 'seat_number', 'from_stop_id', 'to_stop_id'], 'seat_segments_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seat_segments', function (Blueprint $table) {
            $table->dropIndex('seat_segments_lookup_idx');
        });
    }
};
