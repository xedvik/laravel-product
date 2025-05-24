<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'purchase_price',
        'rent_price_per_hour',
    ];

    public function ownerships() {
        return $this->hasMany(Ownership::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
