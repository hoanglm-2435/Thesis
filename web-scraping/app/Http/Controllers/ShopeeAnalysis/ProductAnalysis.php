<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeMall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProductAnalysis extends Controller
{
    public function showProducts($shopId)
    {
        $shop = ShopeeMall::find($shopId);
        $cateID = $shop->category()->first()->id;

        if (!Cache::has('analysis_at') && $shop->products->count() > 0) {
            $analysisAt = Product::all()->first()->created_at;
        } else {
            $analysisAt = Cache::get('analysis_at');
        }

        return view('product_analysis', compact(['analysisAt', 'shop', 'cateID']));
    }

    public function getProducts($shopId)
    {
        $products = Product::where('shop_id', $shopId)
            ->select('id', 'name', 'url', 'price', 'rating')
            ->groupBy('name')
            ->get();

        $revenues = $this->getRevenue($products);

        return DataTables::of($products)
            ->addColumn('price', function ($value) {
                return number_format($value->price, 0, '', ',');
            })
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        return $revenue->first()->sold;
                    };
                }
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }
            })
            ->addColumn('updated_at', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        $updated_at = new Carbon($revenue->first()->updated_at);
                        $diffTime = Carbon::now()->diffForHumans($updated_at);

                        return $diffTime;
                    };
                }
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
            ->rawColumns(['sold', 'revenue', 'reviews', 'updated_at', 'chart'])
            ->make(true);
    }

    public function getRevenue($products)
    {
        $revenues = array();
        foreach ($products as $product) {
            $revenues[] = DB::table('product_revenue')
                ->selectRaw('name, SUM(sold_per_day) as sold, SUM(revenue_per_day) as revenue, created_at as updated_at')
                ->where('name', $product->name)
                ->get();
        }

        return $revenues;
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

    public function filter(Request $request, $shopId)
    {
        $products = Product::where('shop_id', $shopId)
            ->select('id', 'name', 'url', 'price', 'rating')
            ->groupBy('name')
            ->get();

        $revenues = $this->getRevenue($products);

        $filterType = $request->input('filterType');
        $minRange = $request->input('minRange');
        $maxRange = $request->input('maxRange');

        $products = collect($products)->whereBetween($filterType, [$minRange, $maxRange]);

        return DataTables::of($products)
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        return $revenue->first()->sold;
                    };
                }
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }
            })
            ->addColumn('updated_at', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->name === $value->name) {
                        $updated_at = new Carbon($revenue->first()->updated_at);
                        $diffTime = Carbon::now()->diffForHumans($updated_at);

                        return $diffTime;
                    };
                }
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
            ->rawColumns(['sold', 'revenue', 'reviews', 'updated_at', 'chart'])
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

        $product = Product::findOrFail($productId);

        $sumData = DB::table('product_revenue')
            ->select(
                DB::raw('SUM(sold_per_day) as sum_sold'),
                DB::raw('SUM(revenue_per_day) as sum_revenue'),
                DB::raw('MONTH(created_at) as month')
            )
            ->whereYear('created_at', $year)
            ->where('name', $product->name)
            ->where('shop_id', $product->shop_id)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH OF PRODUCT';
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
