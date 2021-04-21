<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    public $timestamps = false;

    protected $fillable = [
        'shop_id',
        'cate_id',
        'item_id',
        'name',
        'url',
        'price',
        'stock',
        'sold',
        'rating',
        'reviews',
        'created_at'
    ];

    public function shop()
    {
        return $this->belongsTo(ShopeeMall::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
