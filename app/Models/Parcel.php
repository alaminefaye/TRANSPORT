<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Parcel extends Model
{
    protected $fillable = [
        'mail_number',
        'sender_name',
        'sender_phone',
        'sender_client_id',
        'recipient_name',
        'recipient_phone',
        'recipient_client_id',
        'parcel_type',
        'description',
        'amount',
        'parcel_value',
        'photo',
        'destination_id',
        'reception_agency_id',
        'status',
        'retrieved_at',
        'retrieved_by_name',
        'retrieved_by_phone',
        'retrieved_by_cni',
        'retrieved_by_user_id',
        'signature',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'parcel_value' => 'decimal:2',
        'retrieved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($parcel) {
            if (empty($parcel->mail_number)) {
                $date = Carbon::now()->format('Ymd');
                $time = Carbon::now()->format('Hi');
                $parcel->mail_number = 'MAIL-' . $date . '-' . $time;
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function receptionAgency(): BelongsTo
    {
        return $this->belongsTo(ReceptionAgency::class);
    }

    public function retrievedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retrieved_by_user_id');
    }

    public function senderClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'sender_client_id');
    }

    public function recipientClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'recipient_client_id');
    }
}
