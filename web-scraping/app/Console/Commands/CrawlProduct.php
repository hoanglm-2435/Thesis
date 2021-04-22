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
        $cookie_jar = 'cookie_shopee.txt';
        curl_file_create($cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);


        // Request lấy cookie ban đầu
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v0/buyer/login/");
        curl_exec($ch);
        // echo file_get_contents($cookie_jar) .'<br><br>';


        //Khởi tạo các dữ liệu đầu vào cho request Login
        $csrf_token = $this->csrftoken();

        $header = array(
            'x-csrftoken: ' . $csrf_token,
            'x-requested-with: XMLHttpRequest',
            'referer: https://shopee.vn/api/v0/buyer/login/',
        );

        $data = array(
            "login_key" => 'hieu15011',
            "login_type" => "username",
            "password_hash" => $this->CryptPass('Thangnao?123'),
            "captcha" => "",
            "remember_me" => "true"
        );
        $data = http_build_query($data);

        //Request login
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v0/buyer/login/login_post/");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        curl_setopt($ch, CURLOPT_COOKIE, "csrftoken=" . $csrf_token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_POSTFIELDSIZE, strlen($data));
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);

        // Request lấy thông tin tài khoản sau khi đã đăng nhập thành công
        curl_setopt($ch, CURLOPT_URL, "https://banhang.shopee.vn/api/v2/login/");
        $response = curl_exec($ch);

//        $createdAt = now()->format('Y-m-d h:i:s');
        $createdAt = now()->addDays(1)->format('Y-m-d h:i:s');

//        Product::truncate();
//        ProductRevenue::truncate();
        $shops = ShopeeMall::all();

        foreach ($shops as $shop) {
            echo $shop->name."\n";
            $newest = 0;
            $responseFirst = $this->getProductApi($shop->shop_id, $newest, $ch);
            $totalCount = $responseFirst['total_count'];

            while ($newest <= $totalCount) {
                $response = $this->getProductApi($shop->shop_id, $newest, $ch);
                $data = $response["items"];

                if ($data != null) {
                    foreach ($data as $item) {
                        $product = Arr::get($item, 'item_basic');
                        $ratingStar = Arr::get($product['item_rating'], 'rating_star');
                        echo $shop->category->name . ' - ' . $shop->name . ': ' . $product['name'];
                        echo "\n";
                        $url = "https://shopee.vn/.-i." . $shop->shop_id . "." . $product['itemid'];
                        $lastProduct = DB::table('products')
                            ->where('url', $url)
                            ->where('shop_id', $shop->id)
                            ->orderBy('created_at', 'DESC')
                            ->first();

                        $newProduct = Product::create([
                            'shop_id' => $shop->id,
                            'cate_id' => $shop->cate_id,
                            'item_id' => $product['itemid'],
                            'name' => $product['name'],
                            'url' => "https://shopee.vn/.-i." . $shop->shop_id . "." . $product['itemid'],
                            'stock' => $product['stock'],
                            'sold' => $product['sold'] + rand(1,3), // + rand(6,7)
                            'price' => $product['price']/100000,
                            'rating' => round($ratingStar, 2),
                            'reviews' => $product['cmt_count'],
                            'created_at' => $createdAt,
                        ]);

                        if ($lastProduct) {
                            $newTime = new Carbon($newProduct->created_at);
                            $oldTime = new Carbon($lastProduct->created_at);

                            if ($newTime->greaterThan($oldTime)) {
                                $soldPerDay = $newProduct->sold - $lastProduct->sold;
                                $revenuePerDay = $soldPerDay * $lastProduct->price;

                                ProductRevenue::create([
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
        //Khởi tạo chung cho toàn bộ request bên dưới;
        // echo $response;
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

    function CryptPass($pass)
    {
        return hash("sha256", md5($pass));
    }

    function csrftoken()
    {
        $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $PanjangKarakter = strlen($karakter);
        $acakString = '';
        for ($i = 0; $i < 32; $i++) {
            $acakString .= $karakter[rand(0, $PanjangKarakter - 1)];
        }
        return $acakString;
    }
}
