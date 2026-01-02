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
        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('departure_city_id')->nullable()->after('route_number')->constrained('villes')->onDelete('restrict');
            $table->foreignId('arrival_city_id')->nullable()->after('departure_city_id')->constrained('villes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['departure_city_id']);
            $table->dropForeign(['arrival_city_id']);
            $table->dropColumn(['departure_city_id', 'arrival_city_id']);
        });
    }
};
