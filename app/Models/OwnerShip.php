<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\OwnershipStatus;

class OwnerShip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'type',
        'status',
        'unique_code',
        'rental_expires_at',
        'amount_paid',
    ];

    protected $casts = [
        'rental_expires_at' => 'datetime',
        'status' => OwnershipStatus::class,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
