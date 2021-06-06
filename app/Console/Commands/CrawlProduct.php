<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductRevenue;
use App\Models\ShopeeMall;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CrawlProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl product from shopee mall';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36");
        $cookie_shopee = 'cookie_shopee.txt';
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_shopee);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_shopee);

        $header = array(
            'x-csrftoken: ' . $this->csrftoken(),
            'x-requested-with: XMLHttpRequest',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=" . $this->csrftoken());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_exec($ch);

        $createdAt = now()->format('Y-m-d h:i:s');

        // Product::truncate();
        // ProductRevenue::truncate();
        $shops = ShopeeMall::selectRaw('id, name, cate_id, shop_id, product_count')->get();

        foreach ($shops as $shop) {
            $newest = 0;
            $totalCount = $shop->product_count;

            while ($totalCount && $newest <= $totalCount) {
                $response = $this->getProductApi($shop->shop_id, $newest, $ch);
                $data = $response["items"] ?? null;

                if ($data != null) {
                    foreach ($data as $item) {
                        $product = Arr::get($item, 'item_basic');
                        $ratingStar = Arr::get($product['item_rating'], 'rating_star');

                        $url = "https://shopee.vn/.-i." . $shop->shop_id . "." . $product['itemid'];
                        $lastProduct = DB::table('products')
                            ->where('shop_id', $shop->id)
                            ->where('url', $url)
                            ->latest('id')
                            ->first();

                        $newProduct = Product::create([
                            'shop_id' => $shop->id,
                            'cate_id' => $shop->cate_id,
                            'item_id' => $product['itemid'],
                            'name' => $product['name'],
                            'url' => "https://shopee.vn/.-i." . $shop->shop_id . "." . $product['itemid'],
                            'stock' => $product['stock'],
                            'sold' => $product['sold'],
                            'price' => $product['price']/100000,
                            'rating' => round($ratingStar, 2),
                            'reviews' => $product['cmt_count'],
                            'created_at' => $createdAt,
                        ]);
                        echo $shop->category->name . ': ' . $product['name'];
                        echo "\n";

                        if ($lastProduct && ($newProduct->sold > $lastProduct->sold)) {
                            $newTime = new Carbon($newProduct->created_at);
                            $oldTime = new Carbon($lastProduct->created_at);
                            echo '----> New Revenue!!!';
                            echo "\n";

                            if ($newTime->greaterThan($oldTime)) {
                                $soldPerDay = $newProduct->sold - $lastProduct->sold;
                                $revenuePerDay = $soldPerDay * $lastProduct->price;

                                DB::table('product_revenue')->insert([
                                    'shop_id' => $shop->id,
                                    'product_id' => $newProduct->id,
                                    'cate_id' => $shop->cate_id,
                                    'name' => $product['name'],
                                    'url' => $newProduct->url,
                                    'price' => $newProduct->price,
                                    'sold_per_day' => $soldPerDay,
                                    'revenue_per_day' => $revenuePerDay,
                                    'created_at' => $newProduct->created_at,
                                ]);
                            }
                        }
                    }
                }

                $newest += 30;
            }
        }
        echo "Crawl success!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
        echo "\n";

        return 0;
    }

    function getProductApi($shopId, $newest, $ch)
    {
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_POST, 0);
        echo "https://shopee.vn/api/v4/search/search_items?by=pop&limit=30&match_id="
            . $shopId . "&newest=" . $newest . "&order=desc&page_type=shop&version=2";
        echo "\n";
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v4/search/search_items?by=pop&limit=30&match_id="
            . $shopId . "&newest=" . $newest . "&order=desc&page_type=shop&version=2");
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        return (json_decode($body, true));
    }

    function csrftoken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        return substr(str_shuffle($chars),0, strlen($chars));
    }
}
