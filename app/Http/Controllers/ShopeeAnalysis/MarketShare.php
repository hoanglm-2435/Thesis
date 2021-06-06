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

        $categories = ShopeeCategory::selectRaw('id, name')->get();

        $cateName = array();
        $color = array();
        $sumData = array();
        $totalShop = array();
        $totalProduct = array();
        foreach ($categories as $cate) {
            $cateName[] = $cate->name;
            $color[] = '#' . dechex(rand(0,10000000));
            $totalShop[] = $cate->shops()->count();
            $totalProduct[] = $cate->shops()->sum('product_count');

            $sumData[] = DB::table('product_revenue')
                ->select(
                    DB::raw('cate_id'),
                    DB::raw('SUM(sold_per_day) as sum_sold'),
                    DB::raw('SUM(revenue_per_day) as sum_revenue'),
                )
                ->whereYear('created_at', $year)
                ->where('cate_id', $cate->id)
                ->groupBy('cate_id', DB::raw('EXTRACT(YEAR FROM created_at)'))
                ->first();
        }

        $cateCount = $categories->count();
        $sumRevenue = array_fill(0, $cateCount, 0);
        $sumSold = array_fill(0, $cateCount, 0);

        foreach ($sumData as $key => $sumForCate) {
            $sumRevenue[$key] = $sumForCate->sum_revenue ?? 0;
            $sumSold[$key] = $sumForCate->sum_sold ?? 0;
        }

        $dataChart = [
            'labels' => $cateName,
            'color' => $color,
            'total_sold' => $sumSold,
            'total_revenue' => $sumRevenue,
            'total_shop' => $totalShop,
            'total_product' => $totalProduct,
        ];

        return response()->json($dataChart);
    }
}
