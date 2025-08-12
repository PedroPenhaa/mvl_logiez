<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FedexLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fedex_labels';

    protected $fillable = [
        'user_id',
        'tracking_number',
        'label_url',
        'status',
        'api_response',
        'request_data',
        'shipping_cost',
        'service_type',
        'recipient_name',
        'recipient_address',
        'recipient_city',
        'recipient_state',
        'recipient_country',
        'recipient_postal_code',
    ];

    protected $casts = [
        'api_response' => 'array',
        'request_data' => 'array',
        'shipping_cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'tracking_number', 'tracking_number');
    }
}
