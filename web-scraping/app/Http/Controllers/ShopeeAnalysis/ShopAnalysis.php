<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\ShopeeCategory;
use App\Models\ShopeeMall;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ShopAnalysis extends Controller
{
    public function showShop($cateId)
    {
        $cate = ShopeeCategory::find($cateId);


        return view('shopee_analysis', compact('cate'));
    }

    public function getShop($cateId)
    {
        $shops = ShopeeMall::where('cate_id', $cateId)
            ->select('id', 'name', 'url')
            ->get();

        $revenues = array();

        foreach ($shops as $shop) {
            $revenues[] = DB::table('product_revenue')
                ->selectRaw('shop_id, count(*) as product_count, SUM(sold_per_day) as sold, SUM(revenue_per_day) as revenue')
                ->where('shop_id', $shop->id)
                ->get();
        }

        return DataTables::of(collect($shops))
            ->addColumn('product_count', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->shop_id === $value->id) {
                        return $revenue->first()->product_count;
                    };
                }
            })
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->shop_id === $value->id) {
                        return $revenue->first()->sold;
                    };
                }
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->shop_id === $value->id) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }
            })
            ->addColumn('products', function ($value) {
                $url = route('shopee.show-products', $value['id']);

                return '
                    <a href="' . $url . '">
                        <button title="See the products of Shop"
                                class="ml-2 btn btn-sm btn-default"
                        >
                            <i class="far fa-eye"></i>
                        </button>
                    </a>
                ';
            })
            ->addColumn('chart', function ($value) {
                $href = route('shop.show-chart', $value->id);

                return '
                    <a href=" ' . $href . ' ">
                        <button
                            title="Analysis Chart"
                            class="ml-2 btn btn-sm btn-default"
                        >
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    </a>
                ';
            })
            ->rawColumns(['product_count', 'sold', 'revenue', 'products', 'chart'])
            ->make(true);
    }

    public function showChart($shopId)
    {
        $shop = ShopeeMall::find($shopId);

        return view('shop_chart', compact('shop'));
    }

    public function getChart($shopId)
    {
        $year = now()->year;

        $sumData = DB::table('product_revenue')
            ->select(
                DB::raw('SUM(sold_per_day) as sum_sold'),
                DB::raw('SUM(revenue_per_day) as sum_revenue'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', $year)
            ->where('shop_id', $shopId)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH OF SHOP';
        $revenueLabel = 'Revenue';
        $soldLabel = 'Sold';
        $rightLabel = 'Products';
        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($sumData as $sumForMonth) {
            $sumRevenue[$sumForMonth->month - 1] = $sumForMonth->sum_revenue;
            $sumSold[$sumForMonth->month - 1] = $sumForMonth->sum_sold;
        }

        $dataChart = [
            'title_chart' => $titleChart,
            'revenue_label' => $revenueLabel,
            'sold_label' => $soldLabel,
            'right_label' => $rightLabel,
            'total_sold' => $sumSold,
            'total_revenue' => $sumRevenue,
            'labels' => $month,
        ];

        return response()->json($dataChart);
    }
}
