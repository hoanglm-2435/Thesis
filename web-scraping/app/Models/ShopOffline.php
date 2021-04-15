<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOffline extends Model
{
    use HasFactory;

    protected $table = 'shop_offline';

    protected $fillable = [
        'name',
        'rating',
        'location',
        'phone_number',
    ];
}
