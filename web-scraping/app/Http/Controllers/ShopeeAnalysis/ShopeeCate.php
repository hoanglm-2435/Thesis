<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\ShopeeCategory;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ShopeeCate extends Controller
{
    public function showCate()
    {
        return view('shopee_cate');
    }

    public function getCate()
    {
        $categories = ShopeeCategory::all();

        $revenues = array();
        foreach ($categories as $category) {
            $revenues[] = DB::table('product_revenue')
                ->selectRaw('cate_id, count(*) as product_count, SUM(sold_per_day) as sold, SUM(revenue_per_day) as revenue')
                ->where('cate_id', $category->id)
                ->get();
        }

        return DataTables::of($categories)
            ->addColumn('shop_count', function ($value) {
                return $value->shops()->count();
            })
            ->addColumn('product_count', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->cate_id === $value->id) {
                        return $revenue->first()->product_count;
                    };
                }
            })
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->cate_id === $value->id) {
                        return $revenue->first()->sold;
                    };
                }
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->cate_id === $value->id) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }
            })
            ->addColumn('shop_list', function ($value) {
                $url = route('shopee.show-shop', $value['id']);

                return '
                    <a href="' . $url . '">
                        <button title="List shop of category"
                                class="ml-2 btn btn-sm btn-default"
                        >
                            <i class="far fa-eye"></i>
                        </button>
                    </a>
                ';
            })
            ->addColumn('chart', function ($value) {
                $href = route('cate.show-chart', $value->id);

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
            ->rawColumns(['shop_count', 'product_count', 'sold', 'revenue', 'shop_list', 'chart'])
            ->make(true);
    }

    public function showChart($cateId)
    {
        $cate = ShopeeCategory::find($cateId);

        return view('cate_chart', compact('cate'));
    }

    public function getChart($cateId)
    {
        $year = now()->year;

        $sumData = DB::table('product_revenue')
            ->select(
                DB::raw('SUM(sold_per_day) as sum_sold'),
                DB::raw('SUM(revenue_per_day) as sum_revenue'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', $year)
            ->where('cate_id', $cateId)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH OF CATEGORY';
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
