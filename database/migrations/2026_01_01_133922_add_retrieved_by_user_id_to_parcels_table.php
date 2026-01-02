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
            if (!Schema::hasColumn('parcels', 'retrieved_by_user_id')) {
                $table->foreignId('retrieved_by_user_id')->nullable()->after('retrieved_by_cni')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcels', function (Blueprint $table) {
            if (Schema::hasColumn('parcels', 'retrieved_by_user_id')) {
                $table->dropForeign(['retrieved_by_user_id']);
                $table->dropColumn('retrieved_by_user_id');
            }
        });
    }
};
