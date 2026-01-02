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
        if (!Schema::hasTable('parcels')) {
            Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('mail_number')->unique();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('parcel_type');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('parcel_value', 10, 2)->nullable();
            $table->foreignId('destination_id')->constrained('destinations')->onDelete('cascade');
            $table->foreignId('reception_agency_id')->constrained('reception_agencies')->onDelete('cascade');
            $table->enum('status', ['En attente', 'En transit', 'Arrivé', 'Récupéré'])->default('En attente');
            $table->timestamp('retrieved_at')->nullable();
            $table->string('retrieved_by_name')->nullable();
            $table->string('retrieved_by_cni')->nullable();
            $table->text('signature')->nullable(); // Pour stocker la signature (base64 ou chemin)
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
