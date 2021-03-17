# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest


class CrawlServiceItem(scrapy.Item):
    shop_name = scrapy.Field()
    product_count = scrapy.Field()
    average_rate = scrapy.Field()
    follower = scrapy.Field()


class ShopeeSpider(scrapy.Spider):
    name = "shopee"
    allowed_domains = ["shopee.vn"]

    start_urls = ["https://shopee.vn/search_user?keyword=sneakers"]

    script = """
        function main(splash)

            local url = splash.args.url
            assert(splash:go(url))
            assert(splash:wait(5))
            assert(splash:runjs("document.querySelector('button.shopee-icon-button.shopee-icon-button--right').click();"))
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
        item = CrawlServiceItem()
        for data in response.css("div.shopee-search-user-item--full"):
            item["shop_name"] = data.css(
                "div.shopee-search-user-item__nickname ::text").extract_first()
            item["product_count"] = data.css(
                "div.shopee-search-user-seller-info-item__header > span.shopee-search-user-seller-info-item__primary-text ::text").extract_first()
            item["average_rate"] = data.css(
                "svg.icon-rating + span.shopee-search-user-seller-info-item__primary-text ::text").extract_first()
            item["follower"] = data.css(
                "div.shopee-search-user-item__follow-count > span:nth-child(1) ::text").extract_first()
            yield item

        yield SplashRequest(
            url=response.url,
            callback=self.parse,
            meta={
                "splash": {
                    "endpoint": "execute",
                    "args": {
                        'wait': 5,
                        "lua_source": self.script,
                    }
                }
            },
            dont_filter=True,
        )
