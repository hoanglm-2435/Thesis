<?php

namespace App\Http\Controllers\ShopOffline;

use App\Http\Controllers\Controller;
use App\Models\ShopOffline;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class GoogleMaps extends Controller
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

    public function filter(Request $request)
    {
        $minRange = $request->input('minRange');
        $maxRange = $request->input('maxRange');

        $shop = ShopOffline::whereBetween('rating', [$minRange, $maxRange])
            ->get();

        return Datatables::of($shop)->make(true);
    }
}
