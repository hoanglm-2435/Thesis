<?php

use App\Http\Controllers\ShopeeAnalysis\Analysis;
use App\Http\Controllers\ShopOffline\GoogleMaps;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'prefix' => 'shopee-analysis',
    'namespace' => 'ShopeeAnalysis',
], function () {
    Route::get('/', [Analysis::class, 'showShop'])->name('shopee');

    Route::get('/shops', [Analysis::class, 'getShop'])->name('shopee.shops');

    Route::group([
        'prefix' => 'shop',
    ], function () {
        Route::get('/{id}', [Analysis::class, 'showProducts'])->name('shopee.shop');

        Route::get('/{id}/products', [Analysis::class, 'getProducts'])->name('shopee.products');

        Route::post('/filter/{id}', [Analysis::class, 'filter'])->name('filter.products');

        Route::get('/comments/{id}', [Analysis::class, 'getComments'])->name('product.comments');
    });
});

Route::group([
    'prefix' => 'shop-offline',
    'namespace' => 'ShopOffline',
], function () {
    Route::get('/', [GoogleMaps::class, 'showShop'])->name('shop-offline');

    Route::get('/shop', [GoogleMaps::class, 'getShop'])->name('shop-offline.shop');

    Route::post('/shop/filter', [GoogleMaps::class, 'filter'])->name('shop-offline.filter');
});
