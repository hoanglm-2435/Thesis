<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CrawlComment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:comment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl comment of products';

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

        $createdAt = now()->format('Y-m-d h:i:s');

        Comment::truncate();
        $products = Product::all();

        foreach ($products as $product) {
            echo $product->name . "\n";
            $productUrl = explode('.', $product->url);
            $itemId = intval(array_pop($productUrl));

            $offset = 0;
            while ($offset <= $product->reviews) {
                $response = $this->getCommentApi($product->shop->shop_id, $itemId, $offset, $ch);
                $data = Arr::get($response['data'], 'ratings');

                if ($data) {
                    foreach ($data as $item) {
                        if ($item['comment']) {
                            echo $item['author_username'];
                            echo "\n";
                            $time = date('Y-m-d h:i:s', $item['mtime']);

                            Comment::create([
                                'product_id' => $product->id,
                                'author' => $item['author_username'],
                                'rating' => $item['rating_star'],
                                'content' => $item['comment'],
                                'time' => $time,
                            ]);
                        } else {
                            echo "Comment is null!!!!!!!!!!!!!!!!!!!!!!!!";
                            echo "\n";
                        }
                    }
                } else {
                    break;
                }
                $offset += 6;
            }
        }
        return 0;
    }

    function getCommentApi($shopId, $itemId, $offset, $ch)
    {
        //Khởi tạo chung cho toàn bộ request bên dưới;
        // echo $response;
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_POST, 0);
        echo "https://shopee.vn/api/v2/item/get_ratings?filter=0&flag=1&itemid="
            . $itemId . "&limit=6&offset=" . $offset . "&shopid=" . $shopId . "&type=0";
        echo "\n";
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v2/item/get_ratings?filter=0&flag=1&itemid="
            . $itemId . "&limit=6&offset=" . $offset . "&shopid=" . $shopId . "&type=0");
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
