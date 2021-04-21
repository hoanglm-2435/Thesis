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
        $cookie_jar = 'cookie_shopee.txt';
        curl_file_create($cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);


        // Request lấy cookie ban đầu
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v0/buyer/login/");
        curl_exec($ch);

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
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);

        // Request lấy thông tin tài khoản sau khi đã đăng nhập thành công
        curl_setopt($ch, CURLOPT_URL, "https://banhang.shopee.vn/api/v2/login/");
        $response = curl_exec($ch);

        ShopeeMall::truncate();
        $categories = ShopeeCategory::all();

        foreach ($categories as $cate) {
            echo $cate->name."\n";
            $response = $this->getShopApi($cate->cate_id, $ch);
            $data = $response["data"]['brands'];

            if ($data != null) {
                foreach ($data as $item) {
                    $shops = Arr::get($item, 'brand_ids');

                    if ($shops) {
                        foreach ($shops as $shop) {
                            echo $shop['brand_name'];
                            echo "\n";

                            ShopeeMall::updateOrCreate([
                                'name' => $shop['brand_name'],
                                'url' => 'https://shopee.vn/' . $shop['username'],
                                'cate_id' => $cate->cate_id,
                                'shop_id' => $shop['shopid'],
                            ]);
                        }
                    }
                }
            }
        }

        return 0;
    }

    function getShopApi($cateId, $ch)
    {
        //Khởi tạo chung cho toàn bộ request bên dưới;
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
