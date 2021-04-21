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
        'cate_id',
        'shop_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function category()
    {
        return $this->belongsTo(ShopeeCategory::class, 'cate_id');
    }
}
