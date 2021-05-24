<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopeeCategory extends Model
{
    use HasFactory;

    protected $table = 'shopee_categories';

    public $timestamps = false;

    protected $fillable = [
        'cate_id',
        'name',
        'url',
    ];

    public function shops()
    {
        return $this->hasMany(ShopeeMall::class, 'cate_id');
    }

    public function products()
    {
        return $this->hasManyThrough(
          Product::class,
          ShopeeMall::class,
          'cate_id',
          'shop_id',
        );
    }
}
