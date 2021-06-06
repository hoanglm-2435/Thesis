<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeMall;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ProductAnalysis extends Controller
{
    public function showProducts($shopId)
    {
        $shop = ShopeeMall::find($shopId);
        $cateID = $shop->category()->first()->id;

        $priceMax = DB::table('products')
            ->selectRaw('shop_id, MAX(price) as price_max')
            ->where('shop_id', $shopId)
            ->groupBy('shop_id')
            ->get();
        $priceMax = $priceMax[0]->price_max;

        return view('product_analysis', compact(['shop', 'cateID', 'priceMax']));
    }

    public function getProducts($shopId)
    {
//        Product::where('cate_id', '!=', 8)->where('created_at', '<', '2021-06-09 01:01:13')->whereMonth('created_at', '=', '06')->delete();
        $products = DB::table('products')
            ->selectRaw('id, name, url, price, rating, shop_id')
            ->where('shop_id', $shopId)
            ->get()
            ->unique('url');

        $revenues = $this->getRevenue($products);

        return DataTables::of($products)
            ->addColumn('price', function ($value) {
                return number_format($value->price, 0, '', ',');
            })
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        return $revenue->first()->sold;
                    };
                }

                return 0;
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }

                return 0;
            })
            ->addColumn('updated_at', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        $updated_at = new Carbon($revenue->first()->updated_at);
                        $diffTime = Carbon::now()->diffForHumans($updated_at);

                        return $diffTime;
                    };
                }

                return 'Not update';
            })
            ->addColumn('reviews', function ($value) {
                $comment = DB::table('comments')
                    ->where('product_id', $value->id)
                    ->get();
                if ($comment && $comment->count() > 0) {
                    return '
                        <button title="Comments of product" data-toggle="modal"
                                id="list-comments"
                                class="ml-2 btn btn-sm btn-default"
                                data-id="' . $value->id . '"
                                data-target="#commentModal" href="#">
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                } else {
                    return '
                        <button title="This product has no reviews"
                                class="ml-2 btn btn-sm btn-default"
                                disabled>
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                }
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
            $revenue = DB::table('product_revenue')
                ->selectRaw('url, SUM(sold_per_day) as sold, SUM(revenue_per_day) as revenue, MAX(created_at) as updated_at')
                ->where('url', $product->url)
                ->whereMonth('created_at', now()->month)
                ->groupBy('url')
                ->get();

            if ($revenue->first() && $revenue->first()->url) {
                $revenues[] = $revenue;
            }
        }

        return $revenues;
    }

    public function getComments($productId)
    {
        $productGroup = Product::find($productId);
        $firstProduct = Product::where('url', $productGroup->url)->first();

        $product = [
            'name' => $firstProduct->name,
            'url' => $firstProduct->url,
        ];

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
            'product' => $product,
            'comment' => $comment ?? null,
        ]);
    }

    public function filter(Request $request, $shopId)
    {
        $products = DB::table('products')
            ->selectRaw('id, name, url, price, rating, shop_id')
            ->where('shop_id', $shopId)
            ->get()
            ->unique('url');

        $revenues = $this->getRevenue($products);

        $filterType = $request->input('filterType');
        $minRange = $request->input('minRange');
        $maxRange = $request->input('maxRange');

        $products = collect($products->unique('name'))->whereBetween($filterType, [$minRange, $maxRange]);

        return DataTables::of($products->unique('name'))
            ->addColumn('sold', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        return $revenue->first()->sold;
                    };
                }

                return 0;
            })
            ->addColumn('revenue', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        return number_format($revenue->first()->revenue, 0, '', ',');
                    };
                }

                return 0;
            })
            ->addColumn('updated_at', function ($value) use ($revenues) {
                foreach ($revenues as $revenue) {
                    if($revenue->first()->url === $value->url) {
                        $updated_at = new Carbon($revenue->first()->updated_at);
                        $diffTime = Carbon::now()->diffForHumans($updated_at);

                        return $diffTime;
                    };
                }

                return 'Not update';
            })
            ->addColumn('reviews', function ($value) {
                $comment = DB::table('comments')
                    ->where('product_id', $value->id)
                    ->get();
                if ($comment && $comment->count() > 0) {
                    return '
                        <button title="Comments of product" data-toggle="modal"
                                id="list-comments"
                                class="ml-2 btn btn-sm btn-default"
                                data-id="' . $value->id . '"
                                data-target="#commentModal" href="#">
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                } else {
                    return '
                        <button title="This product has no reviews"
                                class="ml-2 btn btn-sm btn-default"
                                disabled>
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                }
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
                DB::raw('EXTRACT(MONTH FROM created_at) as month')
            )
            ->whereYear('created_at', $year)
            ->where('url', $product->url)
            ->where('shop_id', $product->shop_id)
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->get();

        $sumRevenue = array_fill(0, 11, 0);
        $sumSold = array_fill(0, 11, 0);

        $titleChart = 'STATISTICS REVENUE AND SOLD BY MONTH OF THIS PRODUCT IN ' . date('Y');
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
