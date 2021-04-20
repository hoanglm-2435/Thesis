# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest
from crawl_service.items import ShopeeCategoryItem


class ShopeeCategorySpider(scrapy.Spider):
    name = "shopee_category"
    allowed_domains = ["shopee.vn"]

    start_urls = [
        "https://shopee.vn/mall/brands",
    ]

    script = """
        function main(splash)

            local url = splash.args.url
            assert(splash:go(url))
            assert(splash:wait(5))

            return {
                html = splash:html(),
                url = splash:url(),
            }
        end
        """

    def start_requests(self):
        for url in self.start_urls:
            yield SplashRequest(
                url,
                endpoint="render.html",
                callback=self.parse,
                args={
                    'wait': 5,
                    "lua_source": self.script,
                },
                dont_filter=True,
            )

    def parse(self, response):
        item = ShopeeCategoryItem()
        
        for data in response.css("li.official-shop-brand-list__category-item"):
            item["name"] = data.css(
                "div.official-shop-brand-list__category-name ::text").extract_first()
            brandUrl = data.css(
                    "a.official-shop-brand-list__category-link ::attr(href)").extract_first()
            item['cate_id'] = int(brandUrl.rsplit('/', 1)[1])
            item['url'] = 'https://shopee.vn' + brandUrl
            

            yield item
