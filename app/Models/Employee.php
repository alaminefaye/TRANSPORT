<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'matricule',
        'name',
        'email',
        'phone',
        'address',
        'position',
        'hire_date',
        'status',
        'salary',
        'photo',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            // Générer le matricule si non fourni
            if (empty($employee->matricule)) {
                $employee->matricule = static::generateMatricule($employee->position);
            }
        });
    }

    /**
     * Génère un matricule au format MAT-XXXXX-L
     * où XXXXX est un numéro séquentiel (5 chiffres) et L est la première lettre du poste
     */
    public static function generateMatricule(?string $position = null): string
    {
        // Obtenir le nombre total d'employés pour générer le numéro séquentiel
        $employeeCount = static::count();
        $nextNumber = $employeeCount + 1;
        
        // Formater le numéro avec 5 chiffres avec zéros devant
        $numberPart = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        // Obtenir la première lettre du poste en majuscule
        $positionLetter = 'X'; // Par défaut si pas de poste
        if (!empty($position) && is_string($position)) {
            $positionLetter = strtoupper(substr(trim($position), 0, 1));
            // Si ce n'est pas une lettre, utiliser X par défaut
            if (!preg_match('/[A-Z]/', $positionLetter)) {
                $positionLetter = 'X';
            }
        }
        
        return "MAT-{$numberPart}-{$positionLetter}";
    }

    /**
     * Relation avec User (optionnelle)
     * Note: Cette relation nécessite une colonne employee_id dans la table users
     * Pour l'activer, créez une migration pour ajouter cette colonne
     * 
     * Pour l'instant, la relation est désactivée car la colonne n'existe pas
     */
    // public function user(): HasOne
    // {
    //     return $this->hasOne(User::class, 'employee_id');
    // }
}
