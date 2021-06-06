<?php

namespace App\Console\Commands;

use App\Models\ShopeeCategory;
use App\Models\ShopeeMall;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CrawlShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl shopee mall on Shopee';

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

//        ShopeeMall::truncate();
        $categories = ShopeeCategory::all();

        foreach ($categories as $cate) {
            $response = $this->getShopApi($cate->cate_id, $ch);
            $data = $response["data"]['brands'];

            if ($data != null) {
                foreach ($data as $item) {
                    $shops = Arr::get($item, 'brand_ids');

                    if ($shops) {
                        foreach ($shops as $shop) {
                            $productCount = Arr::get($this->getProductCountApi($shop['shopid'], $ch), 'total_count', 0);

                            $data = ShopeeMall::updateOrCreate([
                                'shop_id' => $shop['shopid'],
                            ], [
                                'name' => $shop['brand_name'],
                                'url' => 'https://shopee.vn/' . $shop['username'],
                                'cate_id' => $cate->id,
                                'shop_id' => $shop['shopid'],
                                'product_count' => $productCount,
                            ]);
//                            $data->update([
//                                'product_count' => $productCount,
//                            ]);
                            echo $cate->name . ": ";
                            echo $shop['brand_name'] . ' - Total product: ' . $data->product_count;
                            echo "\n";
                        }
                    }
                }
            }
        }

        return 0;
    }

    function getShopApi($cateId, $ch)
    {
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_POST, 0);
        echo "https://shopee.vn/api/v4/official_shop/get_shops_by_category?need_zhuyin=0&category_id=" . $cateId;
        echo "\n";
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v4/official_shop/get_shops_by_category?need_zhuyin=0&category_id=" . $cateId);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        return (json_decode($body, true));
    }

    function getProductCountApi($shopId, $ch)
    {
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_POST, 0);
        echo "https://shopee.vn/api/v4/search/search_items?page_type=shop&match_id=" . $shopId;
        echo "\n";
        curl_setopt($ch, CURLOPT_URL,  "https://shopee.vn/api/v4/search/search_items?page_type=shop&match_id=" . $shopId);
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
