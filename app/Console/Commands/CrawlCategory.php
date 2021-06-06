<?php

namespace App\Console\Commands;

use App\Models\ShopeeCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CrawlCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl list category in Shopee';

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

//        ShopeeCategory::truncate();
        $response = $this->getCategoryApi($ch);
        $data = Arr::get($response['data'], 'category_list');

        foreach ($data as $item) {
            if ($item['catid'] === 91) {
                return 0;
            }
            ShopeeCategory::create([
                'cate_id' => $item['catid'],
                'name' => $item['display_name'],
                'url' => "https://shopee.vn/mall/brands/" . $item['catid'],
            ]);
        }

        echo "Crawl category success!!!";
        echo "\n";

        return 0;
    }

    function getCategoryApi($ch)
    {
        curl_setopt($ch, CURLOPT_PROXY, '');
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, "https://shopee.vn/api/v2/category_list/get");
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
