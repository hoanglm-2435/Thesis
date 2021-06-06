<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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

//        Comment::truncate();
        $lastComment = DB::table('comments')->latest('id')->first();
        $productID = $lastComment->product_id ?? 1;
        $products = Product::selectRaw('min(id) as id, name, url, max(reviews) as reviews, max(shop_id) as shop_id')
            ->where('id', '>=', $productID)
            ->groupBy('name', 'url')
            ->orderBy('id', 'ASC')
            ->get();

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

                            Comment::updateOrCreate([
                                'product_id' => $product->id,
                                'author' => $item['author_username'],
                                'time' => $time,
                            ], [
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

    function csrftoken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        return substr(str_shuffle($chars),0, strlen($chars));
    }
}
