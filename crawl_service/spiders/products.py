# -*- coding: utf-8 -*-
import scrapy
import MySQLdb
from scrapy_splash import SplashRequest
from crawl_service.items import ProductItem
from crawl_service.queries.readdata import dataReader


class ProductsSpider(scrapy.Spider):
    name = "products"
    allowed_domains = ["shopee.vn"]

    start_urls = dataReader(spiderName='shopee_mall')
    # start_urls = [
    #     "https://shopee.vn/shop/76334547/search?shopCollection=10993855",
    #     "https://shopee.vn/depthailannam",
    #     "https://shopee.vn/aokang_flagship_store",
    #     "https://shopee.vn/bentonivietnam.official",
    # ]

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
        for shop_id, url in self.start_urls.items():
            yield SplashRequest(
                url,
                endpoint="render.html",
                callback=self.parse,
                args={
                    'wait': 5,
                    'viewport': '2573x2573',
                },
                meta={
                    'shop_id': shop_id,
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
                    'wait': 5,
                    "lua_source": self.render_script,
                    'viewport': '2573x2573',
                },
                meta={
                    'shop_id': response.meta.get('shop_id'),
                },
                dont_filter=True,
            )

        current_page = int(response.css(
            'span.shopee-mini-page-controller__current ::text').extract_first())
        total_page = int(response.css(
            "span.shopee-mini-page-controller__total ::text").extract_first())
        if current_page < total_page:
            yield SplashRequest(
                url=response.url,
                callback=self.parse,
                meta={
                    "splash": {
                        "endpoint": "execute",
                        "args": {
                            'wait': 5,
                            'url': response.url,
                            "lua_source": self.pagination_script,
                            'viewport': '2573x2573',
                        },
                    },
                    'shop_id': response.meta.get('shop_id'),
                },
                dont_filter=True
            )

    def parse_product(self, response):
        item = ProductItem()

        item["shop_id"] = response.meta.get('shop_id')

        item["name"] = response.css(
            "div.attM6y > span ::text").extract_first()

        priceRange = response.css(
            "div._3e_UQT ::text").extract_first()
        priceRange = priceRange.split()
        if len(priceRange) == 3:
            item["price"] = int((int(priceRange[0].lstrip("₫").replace(
                '.', '')) + int(priceRange[2].lstrip("₫").replace('.', '')))/2)
        else:
            item["price"] = int(priceRange[0].lstrip("₫").replace('.', ''))

        item["rating"] = float(response.css(
            "div.OitLRu._1mYa1t ::text").extract_first() or 0)

        reviews = response.css(
            "div.flex._21hHOx > div:nth-child(2) > div.OitLRu ::text").extract_first() or 0
        if reviews != 0 and reviews[-1] == "k":
            item['reviews'] = int(
                float(reviews.replace('k', '').replace(',', '.'))*1000)
        else:
            item['reviews'] = int(reviews)

        item["stock"] = int(response.xpath(
            "//label[text()='Kho hàng']/following::div[1]/text()").extract_first() or 0)

        sold = response.css(
            "div.aca9MM ::text").extract_first() or 0
        if sold != 0 and sold[-1] == 'k':
            item['sold'] = int(
                float(sold.replace('k', '').replace(',', '.'))*1000)
        else:
            item['sold'] = int(sold)

        yield item
