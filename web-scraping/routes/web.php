<?php

use App\Http\Controllers\ShopeeAnalysis\Analysis;
use App\Http\Controllers\ShopOffline\Get;
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

Route::get('/shopee-analysis', [Analysis::class, 'getShop'])->name('shopee');

Route::get('/shopee-analysis/{id}', [Analysis::class, 'showProducts'])->name('products');

Route::get('/show-comments/{id}', [Analysis::class, 'showComments']);

Route::get('/shop-offline', [Get::class, 'get'])->name('shop-offline');
