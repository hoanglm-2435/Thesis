# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest


class CrawlServiceItem(scrapy.Item):
    product_name = scrapy.Field()
    price = scrapy.Field()
    stock_available = scrapy.Field()
    sold_count = scrapy.Field()
    reviews = scrapy.Field()
    rating = scrapy.Field()


class ProductSpider(scrapy.Spider):
    name = "product"
    allowed_domains = ["shopee.vn"]

    start_urls = ["https://shopee.vn/shop/151338284/search"]

    render_script = '''
    function main(splash)
        splash:init_cookies(splash.args.cookies)
        local num_scrolls = 10
        local scroll_delay = 1

        local scroll_to = splash:jsfunc("window.scrollTo")
        local get_body_height = splash:jsfunc(
            "function() {return document.body.scrollHeight;}"
        )
        assert(splash:go(splash.args.url))

        assert(splash:wait(2))
        for _ = 1, num_scrolls do
            local height = get_body_height()
            for i = 1, 10 do
                scroll_to(0, height * i/10)
                splash:wait(scroll_delay/10)
            end
        end  
        assert(splash:wait(2))

        return {
            cookies = splash:get_cookies(),
            html = splash:html(),
            url = splash:url(),
        }
    end
    '''

    pagination_script = '''
    function main(splash)
        splash:init_cookies(splash.args.cookies)
        local num_scrolls = 10
        local scroll_delay = 1

        local scroll_to = splash:jsfunc("window.scrollTo")
        local get_body_height = splash:jsfunc(
            "function() {return document.body.scrollHeight;}"
        )
        assert(splash:go(splash.args.url))
        assert(splash:wait(2))
        assert(splash:runjs("document.querySelector('button.shopee-icon-button.shopee-icon-button--right').click()"))
        assert(splash:wait(2))

        for _ = 1, num_scrolls do
            local height = get_body_height()
            for i = 1, 10 do
                scroll_to(0, height * i/10)
                splash:wait(scroll_delay/10)
            end
        end  
        assert(splash:wait(2))
        
        return {
            cookies = splash:get_cookies(),
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
                    'wait': 2,
                    'viewport': '3964x3964',
                },
                dont_filter=True
            )

    def parse(self, response):
        list_url = response.css(
            "div.shop-search-result-view__item > a ::attr(href)").extract()
        for item_url in list_url:
            yield SplashRequest(
                url=response.urljoin(item_url),
                endpoint="render.html",
                callback=self.parse_product,
                args={
                    'wait': 2,
                    "lua_source": self.render_script,
                    'viewport': '3964x3964',
                },
                dont_filter=True,
            )

        print("NEXT PAGE")

        yield SplashRequest(
            url=response.url,
            callback=self.parse,
            meta={
                "splash": {
                    "endpoint": "execute",
                    "args": {
                        'wait': 2,
                        'url': response.url,
                        "lua_source": self.pagination_script,
                        'viewport': '3964x3964',
                    },
                }
            },
            dont_filter=True
        )

    def parse_product(self, response):
        item = CrawlServiceItem()

        item["product_name"] = response.css(
            "div.attM6y > span ::text").extract_first()
        item["price"] = response.css(
            "div._3e_UQT ::text").extract_first()
        item["rating"] = response.css(
            "div.OitLRu._1mYa1t ::text").extract_first()
        item["reviews"] = response.css(
            "div.flex._21hHOx > div:nth-child(2) > div.OitLRu ::text").extract_first()
        item["stock_available"] = response.css(
            "div._1afiLm > div:nth-child(3) > div ::text").extract_first()
        item["sold_count"] = response.css(
            "div.aca9MM ::text").extract_first()

        yield item
