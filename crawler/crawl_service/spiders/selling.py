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

    script = '''
    function main(splash)
        local num_scrolls = 10
        local scroll_delay = 1

        local scroll_to = splash:jsfunc("window.scrollTo")
        local get_body_height = splash:jsfunc(
            "function() {return document.body.scrollHeight;}"
        )
        assert(splash:go(splash.args.url))
        assert(splash:wait(5))
        assert(splash:runjs("document.querySelector('button.shopee-icon-button.shopee-icon-button--right').click()"))
        assert(splash:wait(5))

        for _ = 1, num_scrolls do
            local height = get_body_height()
            for i = 1, 10 do
                scroll_to(0, height * i/10)
                splash:wait(scroll_delay/10)
            end
        end  
        assert(splash:wait(5))
        
        return {
            html = splash:html(),
            url = splash:url(),
        }
    end
    '''

    def start_requests(self):
        for url in self.start_urls:
            yield SplashRequest(
                url,
                endpoint="render.html",
                callback=self.parse,
                args={
                    'wait': 5,
                    "lua_source": self.script,
                    'viewport': '3964x3964',
                },
                dont_filter=True
            )

    def parse(self, response):
        item = CrawlServiceItem()
        for data in response.css("div.shopee-search-item-result__item"):
            item["product_name"] = data.css(
                "div._36CEnF ::text").extract_first()
            item["price"] = data.css(
                "div.WTFwws._1lK1eK._5W0f35 span:last-child ::text").extract_first()
            if data.css("div.WTFwws._3f05Zc._3_-SiN"):
                item["price"] = data.css(
                    "div.WTFwws._3f05Zc._3_-SiN ::text").extract_first()
                item["price_sale"] = data.css(
                    "div.WTFwws._1lK1eK._5W0f35 span:last-child ::text").extract_first()
            else:
                item["price"] = data.css(
                    "div.WTFwws._1lK1eK._5W0f35 > span._29R_un:last-child ::text").extract_first()
                item["price_sale"] = data.css(
                    "div.WTFwws._1lK1eK._5W0f35 > span._29R_un:nth-child(2) ::text").extract_first()
            item["sold_count"] = data.css(
                "div.go5yPW ::text").extract_first()
            item["location"] = data.css(
                "div._2CWevj ::text").extract_first()
            yield item

        yield SplashRequest(
            url=response.url,
            callback=self.parse,
            meta={
                "splash": {
                    "endpoint": "execute",
                    "args": {
                        'wait': 5,
                        'url': response.url,
                        "lua_source": self.script,
                        'viewport': '3964x3964',
                    },
                }
            },
            dont_filter=True
        )
