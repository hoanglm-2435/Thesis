<?php

namespace App\Http\Controllers\ShopOffline;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ShopOffline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $shops = DB::table('shop_offline')
            ->select('id', 'place_id', 'name', 'city', 'address', 'phone_number', 'rating', 'user_rating')
            ->get();

        return Datatables::of($shops)
            ->addColumn('reviews', function ($value) {
                $reviews = DB::table('reviews')
                    ->where('shop_offline_id', $value->place_id)
                    ->get();

                if ($reviews && $reviews->count() > 0) {
                    return '
                        <button title="Comment of place" data-toggle="modal"
                                id="list-reviews"
                                class="ml-2 btn btn-sm btn-default"
                                data-id="' . $value->id . '"
                                data-target="#reviewModal" href="#">
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                } else {
                    return '
                        <button title="This place has no reviews"
                                class="ml-2 btn btn-sm btn-default"
                                disabled>
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                }
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
                $reviews = DB::table('reviews')
                    ->where('shop_offline_id', $value->place_id)
                    ->get();

                if ($reviews && $reviews->count() > 0) {
                    return '
                        <button title="Comment of place" data-toggle="modal"
                                id="list-reviews"
                                class="ml-2 btn btn-sm btn-default"
                                data-id="' . $value->id . '"
                                data-target="#reviewModal" href="#">
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                } else {
                    return '
                        <button title="This place has no reviews"
                                class="ml-2 btn btn-sm btn-default"
                                disabled>
                            <i class="far fa-eye"></i>
                        </button>
                    ';
                }
            })
            ->rawColumns(['reviews'])
            ->make(true);
    }
}
