<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_type',
        'nickname',
        'name',
        'phone',
        'email',
        'address',
        'address_complement',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
        'is_residential'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_residential' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
