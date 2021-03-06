<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\ShopeeCategory;
use Carbon\Carbon;
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
        $categories = ShopeeCategory::selectRaw('id, name, url')->get();

        $revenues = array();
        foreach ($categories as $category) {
            $revenue = DB::table('product_revenue')
                ->selectRaw('cate_id, MAX(created_at) as updated_at, SUM(sold_per_day) as sold, SUM(revenue_per_day) as revenue')
                ->where('cate_id', $category->id)
                ->whereMonth('created_at', now()->month)
                ->groupBy('cate_id')
                ->get();

            if ($revenue->first() && $revenue->first()->cate_id) {
                $revenues[] = $revenue;
            }
        }

        return DataTables::of($categories)
            ->addColumn('shop_count', function ($value) {
                return $value->shops()->count();
            })
            ->addColumn('product_count', function ($value) {
                return $value->shops()->sum('product_count');
            })
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if ($revenue->first()->cate_id === $value->id) {
                        return $revenue->first()->sold;
                    };
                }

                return 0;
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if ($revenue->first()->cate_id === $value->id) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }

                return 0;
            })
            ->addColumn('updated_at', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if ($revenue->first()->cate_id === $value->id) {
                        $updated_at = new Carbon($revenue->first()->updated_at);
                        $diffTime = Carbon::now()->diffForHumans($updated_at);

                        return $diffTime;
                    };
                }

                return 'Not Update';
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
            ->rawColumns(['shop_count', 'product_count', 'sold', 'revenue', 'shop_list', 'chart', 'updated_at'])
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
                DB::raw('EXTRACT(MONTH FROM created_at) as month')
            )
            ->whereYear('created_at', $year)
            ->where('cate_id', $cateId)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get();

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH OF THIS CATEGORY IN ' . date('Y');
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
