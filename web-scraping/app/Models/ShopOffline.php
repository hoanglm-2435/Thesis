<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOffline extends Model
{
    use HasFactory;

    protected $table = 'shop_offline';

    public $timestamps = false;

    protected $fillable = [
        'place_id',
        'name',
        'city',
        'address',
        'phone_number',
        'rating',
        'user_rating',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'shop_offline_id', 'place_id');
    }
}
