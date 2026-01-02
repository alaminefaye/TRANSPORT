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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_stop_id')->constrained('stops')->onDelete('cascade');
            $table->foreignId('to_stop_id')->constrained('stops')->onDelete('cascade');
            $table->integer('seat_number');
            $table->foreignId('passenger_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('passenger_name');
            $table->string('passenger_phone')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['En attente', 'Embarqué', 'Terminé', 'Annulé'])->default('En attente');
            $table->timestamp('boarding_time')->nullable();
            $table->timestamp('disembarkation_time')->nullable();
            $table->string('qr_code')->unique();
            $table->foreignId('sold_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
