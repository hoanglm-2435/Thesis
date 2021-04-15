<?php

namespace App\Http\Controllers\ShopOffline;

use App\Http\Controllers\Controller;
use App\Models\ShopOffline;

class Get extends Controller
{
    public function get()
    {
        $shops = ShopOffline::all();

        return view('shop_offline', compact('shops'));
    }
}
