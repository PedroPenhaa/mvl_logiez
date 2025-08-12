<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofOfDelivery extends Model
{
    use HasFactory;

    protected $table = 'proof_of_delivery';

    protected $fillable = [
        'shipment_id',
        'document_url',
        'document_type',
        'signed_by',
        'delivery_date',
        'request_date',
        'expiration_date',
        'request_data',
        'response_data',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'request_date' => 'datetime',
        'expiration_date' => 'datetime',
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
