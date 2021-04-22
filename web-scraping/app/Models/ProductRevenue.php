<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRevenue extends Model
{
    use HasFactory;

    protected $table = 'product_revenue';

    public $timestamps = false;

    protected $fillable = [
        'shop_id',
        'product_id',
        'cate_id',
        'name',
        'url',
        'price',
        'sold_per_day',
        'revenue_per_day',
        'created_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(ShopeeMall::class);
    }
}
