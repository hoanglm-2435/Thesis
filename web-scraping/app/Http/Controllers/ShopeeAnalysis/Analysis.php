<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeMall;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class Analysis extends Controller
{
    public function showShop()
    {
        return view('shopee_analysis');
    }

    public function getShop()
    {
        $shops = ShopeeMall::all();

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

        return DataTables::of(collect($shopAnalysis))
            ->addColumn('products', function ($value) {
                $url = route('shopee.shop', $value['id']);

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

    public function showProducts($shopId)
    {
        $shop = ShopeeMall::find($shopId);

        if (!Cache::has('analysis_at') && $shop->products->count() > 0) {
            $analysisAt = Product::all()->first()->created_at->toDayDateTimeString();
        } else {
            $analysisAt = Cache::get('analysis_at');
        }

        return view('product_analysis', compact(['analysisAt', 'shop']));
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
            ->rawColumns(['reviews', 'updated_at'])
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
                where p1.url = '$product->url' and p2.url = '$product->url'
                order by id desc limit 1) as report",
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
}
