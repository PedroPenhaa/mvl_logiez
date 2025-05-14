<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SenderAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_id',
        'name',
        'phone',
        'email',
        'address',
        'address_complement',
        'city',
        'state',
        'postal_code',
        'country',
        'is_residential',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_residential' => 'boolean',
    ];

    /**
     * Get the shipment that owns the sender address.
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
} 