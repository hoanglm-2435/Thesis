<?php

namespace App\Http\Controllers\ShopOffline;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ShopOffline;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use function Psy\sh;

class GoogleMaps extends Controller
{
    public function showShop()
    {
        return view('shop_offline');
    }

    public function getShop()
    {
        $shops = ShopOffline::all();

        return Datatables::of($shops)
            ->addColumn('reviews', function ($value) {
                return '
                    <button title="Quick View" data-toggle="modal"
                            id="list-reviews"
                            class="ml-2 btn btn-sm btn-default"
                            data-id="' . $value->id . '"
                            data-target="#reviewModal" href="#">
                        <i class="far fa-eye"></i>
                    </button>
                ';
            })
            ->rawColumns(['reviews'])
            ->make(true);
    }

    public function getReviews($shopId)
    {
        $shop = ShopOffline::findOrFail($shopId);
        $reviews = Review::where('shop_offline_id', $shop->place_id)->get();

        $review = [];

        foreach ($reviews as $item) {
            $review[] = [
                'author' => $item['author'],
                'rating' => $item['rating'],
                'content' => $item['content'],
                'time' => $item['time'],
            ];
        }

        return response()->json([
            'review' => $review ?? null,
        ]);
    }

    public function filter(Request $request)
    {
        $minRange = $request->input('minRange');
        $maxRange = $request->input('maxRange');

        $shop = ShopOffline::whereBetween('rating', [$minRange, $maxRange])
            ->get();

        return Datatables::of($shop)
            ->addColumn('reviews', function ($value) {
                return '
                    <button title="Quick View" data-toggle="modal"
                            id="list-reviews"
                            class="ml-2 btn btn-sm btn-default"
                            data-id="' . $value->id . '"
                            data-target="#reviewModal" href="#">
                        <i class="far fa-eye"></i>
                    </button>
                ';
            })
            ->rawColumns(['reviews'])
            ->make(true);
    }
}
