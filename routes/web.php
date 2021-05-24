<?php

use App\Http\Controllers\ShopeeAnalysis\MarketShare;
use App\Http\Controllers\ShopeeAnalysis\ProductAnalysis;
use App\Http\Controllers\ShopeeAnalysis\ShopeeCate;
use App\Http\Controllers\ShopeeAnalysis\ShopAnalysis;
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
Auth::routes();

Route::group([
    'prefix' => 'shopee-analysis',
    'namespace' => 'ShopeeAnalysis',
    'middleware' => 'auth',
], function () {
    Route::group([
        'prefix' => 'cate',
    ], function () {
        Route::get('/', [ShopeeCate::class, 'showCate'])->name('shopee.cate');

        Route::get('/get', [ShopeeCate::class, 'getCate'])->name('shopee.get-cate');

        Route::get('/chart/{id}', [ShopeeCate::class, 'showChart'])->name('cate.show-chart');

        Route::get('/chart/{id}/get', [ShopeeCate::class, 'getChart'])->name('cate.get-chart');

        Route::get('/market-share-chart', [MarketShare::class, 'showMarketChart'])->name('market-share.show-chart');

        Route::get('/market-share-chart/get', [MarketShare::class, 'getMarketChart'])->name('market-share.get-chart');
    });

    Route::group([
        'prefix' => 'shop',
    ], function () {
        Route::get('/{id}', [ShopAnalysis::class, 'showShop'])->name('shopee.show-shop');

        Route::get('/{id}/get', [ShopAnalysis::class, 'getShop'])->name('shopee.get-shop');

        Route::get('/chart/{id}', [ShopAnalysis::class, 'showChart'])->name('shop.show-chart');

        Route::get('/chart/{id}/get', [ShopAnalysis::class, 'getChart'])->name('shop.get-chart');
    });

    Route::group([
        'prefix' => 'product',
    ], function () {
        Route::get('/{id}', [ProductAnalysis::class, 'showProducts'])->name('shopee.show-products');

        Route::get('/{id}/get', [ProductAnalysis::class, 'getProducts'])->name('shopee.get-products');

        Route::post('/filter/{id}', [ProductAnalysis::class, 'filter'])->name('filter.products');

        Route::get('/comments/{id}', [ProductAnalysis::class, 'getComments'])->name('product.comments');

        Route::get('/chart/{id}', [ProductAnalysis::class, 'showChart'])->name('product.show-chart');

        Route::get('/chart/{id}/get', [ProductAnalysis::class, 'getChart'])->name('product.get-chart');
    });
});

Route::group([
    'prefix' => 'shop-offline',
    'namespace' => 'ShopOffline',
    'middleware' => 'auth',
], function () {
    Route::get('/', [GoogleMaps::class, 'showShop'])->name('shop-offline');

    Route::get('/shop', [GoogleMaps::class, 'getShop'])->name('shop-offline.shop');

    Route::post('/shop/filter', [GoogleMaps::class, 'filter'])->name('shop-offline.filter');

    Route::get('/reviews/{id}', [GoogleMaps::class, 'getReviews'])->name('shop.reviews');
});
