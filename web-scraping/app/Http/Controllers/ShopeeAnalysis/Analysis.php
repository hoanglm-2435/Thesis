<?php

namespace App\Http\Controllers\ShopeeAnalysis;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ShopeeMall;
use Illuminate\Support\Facades\DB;

class Analysis extends Controller
{
    public function getShop()
    {
        $shops = ShopeeMall::all();

        foreach ($shops as $shop) {
            $report = $this->getReport($shop->products);

            $sold = 0;
            $revenue = 0;
            foreach ($report as $item) {
                $sold += $item[0]->soldPerMonth;
                $revenue += $item[0]->revenuePerMonth;
            }
            $shopAnalysis[] = [
                'shop_id' => $shop->id,
                'name' => $shop->name,
                'url' => $shop->url,
                'sold' => $sold,
                'revenue' => $revenue,
            ];
        }

        return view('shopee_analysis', compact('shopAnalysis'));
    }

    public function showProducts($shopId)
    {
        $products = Product::where('shop_id', $shopId)->get();
        $report = $this->getReport($products);

        foreach($report as $product) {
            $shopId = $product[0]->shop;

            if ($shopId) {
                $product[0]->shop = [
                    'name' => ShopeeMall::find($shopId)->name,
                    'url' => ShopeeMall::find($shopId)->url
                ];
            }
        }

        return view('product_analysis', compact('report'));
    }

    public function showComments($productId)
    {
        $comments = Comment::where('product_id', $productId)
            ->get();

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
        foreach ($products as $key => $product) {
            $report[] = DB::select(DB::raw(
                "SELECT report.id, report.name, report.url, report.shop, report.price, report.rating, report.reviews, SUM(report.soldPerDay) as soldPerMonth, SUM(report.revenuePerDay) as revenuePerMonth FROM
                (Select p1.id, p1.name, p1.url, p1.shop_id as shop, p2.price, p1.rating, p1.reviews, (p1.stock - p2.stock) as soldPerDay, (p1.stock - p2.stock)*p1.price as revenuePerDay FROM crawler_test.products as p1
                inner join products as p2
                on datediff(p2.created_at, p1.created_at) = 1
                where p1.url = '$product->url' and p2.url = '$product->url') as report"
            ));
        }

        $report = array_unique($report, SORT_REGULAR);

        return $report;
    }
}
