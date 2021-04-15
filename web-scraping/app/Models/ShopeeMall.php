<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopeeMall extends Model
{
    use HasFactory;

    protected $table = 'shopee_mall';

    protected $fillable = [
        'name',
        'url',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }
}
