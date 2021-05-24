<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\ShopeeCategory;
use Illuminate\Support\Facades\DB;

class MarketShare extends Controller
{
    public function showMarketChart()
    {
        return view('market_share_chart');
    }

    public function getMarketChart()
    {
        $year = now()->year;

        $categories = ShopeeCategory::all();

        $cateName = array();
        $color = array();
        $sumData = array();
        $totalShop = array();
        foreach ($categories as $cate) {
            $cateName[] = $cate->name;
            $color[] = '#' . dechex(rand(0,10000000));
            $totalShop[] = $cate->shops()->count();

            $sumData[] = DB::table('product_revenue')
                ->select(
                    DB::raw('cate_id'),
                    DB::raw('COUNT(*) as sum_product'),
                    DB::raw('SUM(sold_per_day) as sum_sold'),
                    DB::raw('SUM(revenue_per_day) as sum_revenue'),
                )
                ->whereYear('created_at', $year)
                ->where('cate_id', $cate->id)
                ->groupBy(DB::raw('YEAR(created_at)'))
                ->first();
        }

        $sumRevenue = array_fill(0, $categories->count(), 0);
        $sumSold = array_fill(0, $categories->count(), 0);
        $sumProduct = array_fill(0, $categories->count(), 0);

        foreach ($sumData as $key => $sumForCate) {
            $sumRevenue[$key] = $sumForCate->sum_revenue;
            $sumSold[$key] = $sumForCate->sum_sold;
            $sumProduct[$key] = $sumForCate->sum_product;
        }

        $dataChart = [
            'labels' => $cateName,
            'color' => $color,
            'total_sold' => $sumSold,
            'total_revenue' => $sumRevenue,
            'total_shop' => $totalShop,
            'total_product' => $sumProduct,
        ];

        return response()->json($dataChart);
    }
}
