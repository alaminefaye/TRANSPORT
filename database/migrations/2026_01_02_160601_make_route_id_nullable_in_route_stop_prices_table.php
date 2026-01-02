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
        Schema::table('route_stop_prices', function (Blueprint $table) {
            // Supprimer la clé étrangère d'abord
            $table->dropForeign(['route_id']);
            
            // Supprimer l'ancienne contrainte unique
            $table->dropUnique(['route_id', 'from_stop_id', 'to_stop_id']);
            
            // Modifier la colonne pour la rendre nullable
            $table->unsignedBigInteger('route_id')->nullable()->change();
            
            // Ajouter de nouveau la clé étrangère (nullable cette fois)
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('route_stop_prices', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign(['route_id']);
            
            // Remettre route_id non nullable
            $table->unsignedBigInteger('route_id')->nullable(false)->change();
            
            // Remettre la clé étrangère
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            
            // Remettre la contrainte unique
            $table->unique(['route_id', 'from_stop_id', 'to_stop_id']);
        });
    }
};
