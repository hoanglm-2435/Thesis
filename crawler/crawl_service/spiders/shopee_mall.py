# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest
from crawl_service.items import ShopeeMallItem


class ShopeeMallSpider(scrapy.Spider):
    name = "shopee_mall"
    allowed_domains = ["shopee.vn"]

    start_urls = [
        "https://shopee.vn/mall/brands/13030",
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
        item = ShopeeMallItem()
        for data in response.css("div.full-brand-list-item"):
            item["name"] = data.css(
                "div.full-brand-list-item__brand-name ::text").extract_first()
            item["url"] = "https://shopee.vn" + \
                data.css(
                    "a.full-brand-list-item__brand-cover-image ::attr(href)").extract_first()

            yield item
