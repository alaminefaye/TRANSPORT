<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('parcels')) {
            // Supprimer les anciennes colonnes string si elles existent
            Schema::table('parcels', function (Blueprint $table) {
                if (Schema::hasColumn('parcels', 'destination')) {
                    $table->dropColumn('destination');
                }
                if (Schema::hasColumn('parcels', 'reception_agency')) {
                    $table->dropColumn('reception_agency');
                }
            });

            // Si les colonnes existent déjà, les rendre nullable d'abord
            if (Schema::hasColumn('parcels', 'destination_id')) {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->unsignedBigInteger('destination_id')->nullable()->change();
                });
            } else {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->unsignedBigInteger('destination_id')->nullable()->after('parcel_value');
                });
            }

            if (Schema::hasColumn('parcels', 'reception_agency_id')) {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->unsignedBigInteger('reception_agency_id')->nullable()->change();
                });
            } else {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->unsignedBigInteger('reception_agency_id')->nullable()->after('destination_id');
                });
            }

            // Nettoyer les données invalides (mettre à NULL les IDs qui n'existent pas)
            if (Schema::hasTable('destinations') && Schema::hasColumn('parcels', 'destination_id')) {
                $validDestinationIds = DB::table('destinations')->pluck('id')->toArray();
                if (!empty($validDestinationIds)) {
                    DB::table('parcels')
                        ->whereNotNull('destination_id')
                        ->whereNotIn('destination_id', $validDestinationIds)
                        ->update(['destination_id' => null]);
                } else {
                    // Si aucune destination valide, mettre tous à NULL
                    DB::table('parcels')->update(['destination_id' => null]);
                }
            }
            
            if (Schema::hasTable('reception_agencies') && Schema::hasColumn('parcels', 'reception_agency_id')) {
                $validAgencyIds = DB::table('reception_agencies')->pluck('id')->toArray();
                if (!empty($validAgencyIds)) {
                    DB::table('parcels')
                        ->whereNotNull('reception_agency_id')
                        ->whereNotIn('reception_agency_id', $validAgencyIds)
                        ->update(['reception_agency_id' => null]);
                } else {
                    // Si aucune agence valide, mettre tous à NULL
                    DB::table('parcels')->update(['reception_agency_id' => null]);
                }
            }

            // Maintenant ajouter les foreign keys
            Schema::table('parcels', function (Blueprint $table) {
                if (Schema::hasTable('destinations') && Schema::hasColumn('parcels', 'destination_id')) {
                    try {
                        $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('cascade');
                    } catch (\Exception $e) {
                        // La contrainte existe peut-être déjà, ignorer
                    }
                }
                
                if (Schema::hasTable('reception_agencies') && Schema::hasColumn('parcels', 'reception_agency_id')) {
                    try {
                        $table->foreign('reception_agency_id')->references('id')->on('reception_agencies')->onDelete('cascade');
                    } catch (\Exception $e) {
                        // La contrainte existe peut-être déjà, ignorer
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parcels')) {
            Schema::table('parcels', function (Blueprint $table) {
                // Supprimer les foreign keys si elles existent
                if (Schema::hasColumn('parcels', 'destination_id')) {
                    $table->dropForeign(['destination_id']);
                    $table->dropColumn('destination_id');
                }
                if (Schema::hasColumn('parcels', 'reception_agency_id')) {
                    $table->dropForeign(['reception_agency_id']);
                    $table->dropColumn('reception_agency_id');
                }

                // Restaurer les anciennes colonnes string
                if (!Schema::hasColumn('parcels', 'destination')) {
                    $table->string('destination')->after('parcel_value');
                }
                if (!Schema::hasColumn('parcels', 'reception_agency')) {
                    $table->string('reception_agency')->after('destination');
                }
            });
        }
    }
};
