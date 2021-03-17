# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest


class CrawlServiceItem(scrapy.Item):
    product_name = scrapy.Field()
    price = scrapy.Field()
    price_sale = scrapy.Field()
    rating_number = scrapy.Field()
    location = scrapy.Field()


class LazadaSpider(scrapy.Spider):
    name = "lazada"
    allowed_domains = ["lazada.vn"]

    start_urls = ["https://www.lazada.vn/catalog/?q=sneakers&_keyori=ss&from=input&spm=a2o4n.searchlist.search.go.3efc4504MAePqS"]

    script = """
        function main(splash)

            local url = splash.args.url
            assert(splash:go(url))
            assert(splash:wait(5))
            assert(splash:runjs("document.querySelector('.ant-pagination-next a').click()"))
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
        for data in response.css("div.c2prKC"):
            item["product_name"] = data.css(
                "div.c16H9d > a ::text").extract_first()
            item["price"] = data.css(
                "div.c3KeDq > div.c3gUW0 > span ::text").extract_first()
            item["price_sale"] = data.css(
                "div.c3KeDq > div.c3lr34 > span.c1-B2V > del ::text").extract()
            item["rating_number"] = data.css(
                "div.c3KeDq > div.c15YQ9 > div > span ::text").extract_first()
            item["location"] = data.css(
                "div.c3KeDq > div.c15YQ9 > span ::text").extract_first()
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
