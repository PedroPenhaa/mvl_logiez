<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $table = 'shipping_rates';

    protected $fillable = [
        'carrier',
        'service_code',
        'service_name',
        'origin_country',
        'destination_country',
        'min_weight',
        'max_weight',
        'base_price',
        'price_per_kg',
        'handling_fee',
        'fuel_surcharge',
        'currency',
        'delivery_time_min',
        'delivery_time_max',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'handling_fee' => 'decimal:2',
        'fuel_surcharge' => 'decimal:2',
        'delivery_time_min' => 'integer',
        'delivery_time_max' => 'integer',
        'is_active' => 'boolean',
    ];
}
