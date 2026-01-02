<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'type',
        'motif',
        'montant',
        'invoice_photo',
        'notes',
        'status',
        'validated_by',
        'validated_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
