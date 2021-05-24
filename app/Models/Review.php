<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'place_id',
        'author',
        'rating',
        'content',
        'time',
    ];

    public function shopOffline()
    {
        return $this->belongsTo(ShopOffline::class);
    }
}
