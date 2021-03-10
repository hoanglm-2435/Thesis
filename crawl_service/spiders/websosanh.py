# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest

class CrawlServiceItem(scrapy.Item):
    name = scrapy.Field()
    price = scrapy.Field()
    image = scrapy.Field()


class WebsosanhSpider(scrapy.Spider):
    name = "wss"
    allowed_domains = ["websosanh.vn"]

    start_urls = ["https://websosanh.vn/socola/cat-2053.htm"]

    script = """
        function main(splash)
            local url = splash.args.url
            assert(splash:go(url))
            assert(splash:wait(0.5))
            assert(splash:runjs("$('.next')[0].click();"))
            return {
                html = splash:html(),
                url = splash:url(),
            }
        end
        """

    def start_requests(self):
        for url in self.start_urls:
            yield SplashRequest(url, endpoint="render.html", callback=self.parse, args={'wait': 8}, dont_filter=True)

    def parse(self, response):
        item = CrawlServiceItem()
        for data in response.css("li.product-item"):
            item["name"] = data.css("a h3 ::text").extract_first()
            item["price"] = data.css("span.product-meta span.product-price ::text").extract_first()
            item["image"] = data.css("a > span.product-img > img::attr(src)").extract_first()
            yield item

        yield SplashRequest(
            url=response.url,
            callback=self.parse,
            meta={
                "splash": {"endpoint": "execute", "args": {"lua_source": self.script}}
            },
        )
