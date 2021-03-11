# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest


class CrawlServiceItem(scrapy.Item):
    product_name = scrapy.Field()
    price = scrapy.Field()
    price_sale = scrapy.Field()
    sold_count = scrapy.Field()
    location = scrapy.Field()


class SellingSpider(scrapy.Spider):
    name = "selling"
    allowed_domains = ["shopee.vn"]

    start_urls = ["https://shopee.vn/search?keyword=sneakers&sortBy=sales"]

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
                }
            )

    def parse(self, response):
        item = CrawlServiceItem()
        for data in response.css("div.shopee-search-item-result__item"):
            item["product_name"] = data.css(
                "div._1co5xN ::text").extract_first()
            item["price"] = data.css(
                "div.QmqjGn + div._2vQ-UF ::text").extract_first()
            item["price_sale"] = data.css(
                "div._1w9jLI._1DGuEV._7uLl65 span:last-child ::text").extract()
            item["sold_count"] = data.css(
                "div._245-SC ::text").extract()
            item["location"] = data.css(
                "div._41f1_p ::text").extract()
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
                    },
                }
            },
        )
