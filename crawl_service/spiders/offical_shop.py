# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest
from crawl_service.items import OfficalShopItem

class OfficalShopSpider(scrapy.Spider):
    name = "offical_shop"
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
        item = OfficalShopItem()
        for data in response.css("div.shopee-search-user-item--full"):
            if data.css("div.official-shop-new-badge"):
                item["name"] = data.css(
                    "div.shopee-search-user-item__nickname ::text").extract_first()
                item["url"] = "https://shopee.vn" + data.css("a.shopee-search-user-item__shop-info ::attr(href)").extract_first()
                item["product_count"] = data.css(
                    "div.shopee-search-user-seller-info-item__header > span.shopee-search-user-seller-info-item__primary-text ::text").extract_first()
                item["rate_average"] = data.css(
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
