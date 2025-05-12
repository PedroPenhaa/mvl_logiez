<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'quote_id',
        'tracking_number',
        'shipment_id',
        'carrier',
        'service_code',
        'service_name',
        'label_url',
        'label_format',
        'status',
        'status_description',
        'last_status_update',
        'package_height',
        'package_width',
        'package_length',
        'package_weight',
        'total_price',
        'currency',
        'total_price_brl',
        'ship_date',
        'estimated_delivery_date',
        'delivery_date',
        'is_simulation',
        'was_delivered',
        'has_issues'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'package_height' => 'float',
        'package_width' => 'float',
        'package_length' => 'float',
        'package_weight' => 'float',
        'total_price' => 'float',
        'total_price_brl' => 'float',
        'ship_date' => 'date',
        'estimated_delivery_date' => 'date',
        'delivery_date' => 'date',
        'last_status_update' => 'datetime',
        'is_simulation' => 'boolean',
        'was_delivered' => 'boolean',
        'has_issues' => 'boolean',
    ];

    /**
     * Get the user that owns the shipment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quote associated with the shipment.
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the sender address for the shipment.
     */
    public function senderAddress()
    {
        return $this->hasOne(SenderAddress::class);
    }

    /**
     * Get the recipient address for the shipment.
     */
    public function recipientAddress()
    {
        return $this->hasOne(RecipientAddress::class);
    }

    /**
     * Get the items for the shipment.
     */
    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Get the tracking events for the shipment.
     */
    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }
} 