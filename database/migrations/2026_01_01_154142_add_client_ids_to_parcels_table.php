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
        Schema::table('parcels', function (Blueprint $table) {
            $table->foreignId('sender_client_id')->nullable()->after('sender_phone')->constrained('clients')->onDelete('set null');
            $table->foreignId('recipient_client_id')->nullable()->after('recipient_phone')->constrained('clients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropForeign(['sender_client_id']);
            $table->dropForeign(['recipient_client_id']);
            $table->dropColumn(['sender_client_id', 'recipient_client_id']);
        });
    }
};
