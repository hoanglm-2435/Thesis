<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeMall;
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
                'sold' => $sold,
                'revenue' => $revenue,
            ];
        }

        return DataTables::of(collect($shopAnalysis))->make(true);
    }

    public function showProducts($shopId)
    {
        return view('product_analysis');
    }

    public function getProducts($shopId)
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

        $report = array_filter($report, function ($value) {
            return !is_null($value->id);
        });

        return DataTables::of(collect($report))->make(true);
    }

    public function getComments($productId)
    {
        $comments = Comment::where('product_id', $productId)
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
            $report[] = DB::select(DB::raw(
                "SELECT report.id, report.name, report.url, report.shop, report.price, report.rating, report.reviews, report.created_at, SUM(report.soldPerDay) as soldPerMonth, SUM(report.revenuePerDay) as revenuePerMonth FROM
                (Select p1.id, p2.name, p2.url, p2.shop_id as shop, p2.price, p2.rating, p2.reviews, p2.created_at, (p1.stock - p2.stock) as soldPerDay, (p1.stock - p2.stock)*p1.price as revenuePerDay FROM crawler_test.products as p1
                inner join products as p2
                on datediff(p2.created_at, p1.created_at) = 1
                where p1.url = '$product->url' and p2.url = '$product->url') as report"
            ));
        }

        $report = array_unique($report, SORT_REGULAR);

        return Arr::flatten($report);
    }

    public function filterPrice(Request $request, $shopId)
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

        return DataTables::of(collect($report))->make(true);
    }
}
