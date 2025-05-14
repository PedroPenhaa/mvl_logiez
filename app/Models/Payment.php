<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shipment_id',
        'transaction_id',
        'payment_id',
        'payment_method',
        'payment_gateway',
        'amount',
        'currency',
        'status',
        'payment_date',
        'due_date',
        'payer_name',
        'payer_document',
        'payer_email',
        'invoice_url',
        'barcode',
        'payment_link',
        'qrcode',
        'gateway_response',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'datetime',
        'due_date' => 'datetime',
        'gateway_response' => 'json',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shipment associated with the payment.
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
} 