<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_id',
        'description',
        'weight',
        'quantity',
        'unit_price',
        'total_price',
        'currency',
        'country_of_origin',
        'harmonized_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'float',
        'quantity' => 'integer',
        'unit_price' => 'float',
        'total_price' => 'float',
    ];

    /**
     * Get the shipment that owns the item.
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
} 