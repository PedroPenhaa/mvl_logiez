<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'origin_postal_code',
        'origin_country',
        'destination_postal_code',
        'destination_country',
        'package_height',
        'package_width',
        'package_length',
        'package_weight',
        'cubic_weight',
        'carrier',
        'service_code',
        'service_name',
        'delivery_time_min',
        'delivery_time_max',
        'total_price',
        'base_price',
        'tax_amount',
        'additional_fee',
        'currency',
        'exchange_rate',
        'total_price_brl',
        'request_data',
        'response_data',
        'is_simulation',
        'quote_reference',
        'expires_at',
        'ip_address',
        'user_agent'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'package_height' => 'decimal:2',
        'package_width' => 'decimal:2',
        'package_length' => 'decimal:2',
        'package_weight' => 'decimal:2',
        'cubic_weight' => 'decimal:2',
        'total_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'additional_fee' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'total_price_brl' => 'decimal:2',
        'request_data' => 'json',
        'response_data' => 'json',
        'is_simulation' => 'boolean',
        'expires_at' => 'datetime',
        'delivery_time_min' => 'integer',
        'delivery_time_max' => 'integer',
    ];

    /**
     * Get the user that owns the quote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the shipments for this quote.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
