<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'shop_id',
        'url',
        'name',
        'price',
        'stock',
        'sold',
        'rating',
        'reviews',
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
