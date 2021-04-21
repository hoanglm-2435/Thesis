<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeCategory;
use App\Models\ShopeeMall;
use Carbon\Carbon;
use Facade\Ignition\DumpRecorder\Dump;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class Analysis extends Controller
{
    public function showCate()
    {
        return view('shopee_cate');
    }

    public function getCate()
    {
        $categories = ShopeeCategory::all();

        $cateAnalysis = [];
        foreach ($categories as $cate) {
            $shopAnalysis = $this->shopAnalysis($cate->shops);

            $sold = 0;
            $revenue = 0;
            foreach ($shopAnalysis as $shop) {
                $sold += $shop['sold'];
                $revenue += $shop['revenue'];
            }
            $cateAnalysis[] = [
                'id' => $cate->id,
                'name' => $cate->name,
                'url' => $cate->url,
                'shop_count' => collect($shopAnalysis)->count(),
                'sold' => $sold,
                'revenue' => $revenue,
            ];
        }

        return DataTables::of(collect($cateAnalysis))
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
            ->rawColumns(['shop_list'])
            ->make(true);
    }

    public function showShop($cateId)
    {
        $cate = ShopeeCategory::find($cateId);


        return view('shopee_analysis', compact('cate'));
    }

    public function getShop($cateId)
    {
        $shops = ShopeeMall::where('cate_id', $cateId)->get();

        $shopAnalysis = $this->shopAnalysis($shops);

        return DataTables::of(collect($shopAnalysis))
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
            ->rawColumns(['products'])
            ->make(true);
    }

    public function shopAnalysis($shops)
    {
        $shopAnalysis = [];

        foreach ($shops as $shop) {
            $report = $this->getReport($shop->products);

            $sold = 0;
            $revenue = 0;
            foreach ($report as $item) {
                $sold += $item->soldPerMonth;
                $revenue += $item->revenuePerMonth;
            }
            $shopAnalysis[] = [
                'id' => $shop->id,
                'name' => $shop->name,
                'url' => $shop->url,
                'product_count' => collect($report)->count(),
                'sold' => $sold,
                'revenue' => $revenue,
            ];
        }

        return $shopAnalysis;
    }

    public function showProducts($shopId)
    {
        $shop = ShopeeMall::find($shopId);
        $cateID = $shop->category()->first()->id;

        if (!Cache::has('analysis_at') && $shop->products->count() > 0) {
            $analysisAt = Product::all()->first()->created_at->toDayDateTimeString();
        } else {
            $analysisAt = Cache::get('analysis_at');
        }

        return view('product_analysis', compact(['analysisAt', 'shop', 'cateID']));
    }

    public function getProducts($shopId)
    {
        $products = Product::where('shop_id', $shopId)->get();
        $report = $this->getReport($products);

        $report = array_filter($report, function ($value) {
            return !is_null($value->id);
        });

        return DataTables::of(collect($report))
            ->addColumn('updated_at', function ($value) {
                $updated_at = new Carbon($value->created_at);
                $diffTime = Carbon::now()->diffForHumans($updated_at);

                return $diffTime;
            })
            ->addColumn('reviews', function ($value) {
                return '
                    <button title="Quick View" data-toggle="modal"
                            id="list-comments"
                            class="ml-2 btn btn-sm btn-default"
                            data-id="' . $value->id . '"
                            data-target="#commentModal" href="#">
                        <i class="far fa-eye"></i>
                    </button>
                ';
            })
            ->addColumn('chart', function ($value) {
                $href = route('product.show-chart', $value->id);

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
            ->rawColumns(['reviews', 'updated_at', 'chart'])
            ->make(true);
    }

    public function getComments($productId)
    {
        $productGroup = Product::find($productId);
        $firstProduct = Product::where('url', $productGroup->url)->first();

        $comments = Comment::where('product_id', $firstProduct->id)
            ->get();
        $comment = [];

        foreach ($comments as $item) {
            $comment[] = [
                'author' => $item['author'],
                'rating' => $item['rating'],
                'content' => $item['content'],
                'time' => $item['time'],
            ];
        }

        return response()->json([
            'comment' => $comment ?? null,
        ]);
    }

    public function getReport($products)
    {
        $report = [];

        foreach ($products as $product) {
            $report[] = DB::select("SELECT report.id, report.name, report.url, report.shop, report.price, report.rating, report.created_at, SUM(report.soldPerDay) as soldPerMonth, SUM(report.revenuePerDay) as revenuePerMonth FROM
                (Select p1.id, p2.name, p2.url, p2.shop_id as shop, p2.price, p2.rating, p2.created_at, (p2.sold - p1.sold) as soldPerDay, (p2.sold - p1.sold)*p1.price as revenuePerDay FROM products as p1
                join products as p2
                on datediff(p2.created_at, p1.created_at) = 1
                where p1.url = '$product->url' and p2.url = '$product->url') as report",
            );
        }

        $report = array_unique($report, SORT_REGULAR);

        return Arr::flatten($report);
    }

    public function filter(Request $request, $shopId)
    {
        $products = Product::where('shop_id', $shopId)->get();
        $report = $this->getReport($products);

        foreach($report as $product) {
            $shopId = $product->shop;

            if ($shopId) {
                $product->shop = [
                    'name' => ShopeeMall::find($shopId)->name,
                    'url' => ShopeeMall::find($shopId)->url
                ];
            }
        }

        $filterType = $request->input('filterType');
        $minRange = $request->input('minRange');
        $maxRange = $request->input('maxRange');

        $report = collect($report)->whereBetween($filterType, [$minRange, $maxRange]);

        return DataTables::of(collect($report))
            ->addColumn('updated_at', function ($value) {
                $updated_at = new Carbon($value->created_at);
                $diffTime = Carbon::now()->diffForHumans($updated_at);

                return $diffTime;
            })
            ->addColumn('reviews', function ($value) {
                return '
                    <button title="Quick View" data-toggle="modal"
                            id="list-comments"
                            class="ml-2 btn btn-sm btn-default"
                            data-id="' . $value->id . '"
                            data-target="#commentModal" href="#">
                        <i class="far fa-eye"></i>
                    </button>
                ';
            })
            ->rawColumns(['reviews', 'updated_at'])
            ->make(true);
    }

    public function showChart($productId)
    {
        $product = Product::find($productId);

        return view('product_chart', compact('product'));
    }

    public function getChart($productId)
    {
        $year = now()->year;

        $product = Product::find($productId);

        $sumData = DB::select("SELECT report.id, month(report.created_at) as month, SUM(report.soldPerDay) as sum_sold, SUM(report.revenuePerDay) as sum_revenue FROM
                (Select p1.id, p2.created_at, (p2.sold - p1.sold) as soldPerDay, (p2.sold - p1.sold)*p1.price as revenuePerDay FROM products as p1
                join products as p2
                on datediff(p2.created_at, p1.created_at) = 1
                where p1.url = '$product->url' and p2.url = '$product->url') as report
                where year(report.created_at) = '$year'
                group by month(report.created_at)",
        );

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH';
        $revenueLabel = 'Revenue';
        $soldLabel = 'Sold';
        $rightLabel = 'Products';
        $month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($sumData as $key => $sumForMonth) {
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
