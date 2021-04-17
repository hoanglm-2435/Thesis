<?php

namespace App\Http\Controllers\ShopOffline;

use App\Http\Controllers\Controller;
use App\Models\ShopOffline;
use Yajra\DataTables\DataTables;

class Get extends Controller
{
    public function showShop()
    {
        return view('shop_offline');
    }

    public function getShop()
    {
        $shops = ShopOffline::all();

        return Datatables::of($shops)->make(true);
    }
}
