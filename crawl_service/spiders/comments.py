# -*- coding: utf-8 -*-
import scrapy
import MySQLdb
from scrapy_splash import SplashRequest
from crawl_service.items import CommentItem
from crawl_service.queries.readdata import dataReader


class CommentsSpider(scrapy.Spider):
    name = "comments"
    allowed_domains = ["shopee.vn"]

    start_urls = dataReader(spiderName='products')

    pagination_script = '''
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
        splash:set_viewport_full()
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
        for product_id, url in self.start_urls.items():
            yield SplashRequest(
                url,
                endpoint="render.html",
                callback=self.parse,
                args={
                    'wait': 5,
                    'viewport': '2573x2573',
                },
                meta={
                    'product_id': product_id,
                },
                dont_filter=True
            )

    def parse(self, response):
        item = CommentItem()
        
        for comment in response.css("div.shopee-product-rating"):
            item["product_id"] = response.meta.get('product_id')
            
            item["author"] = comment.css("a.shopee-product-rating__author-name ::text").extract_first()
            
            ratings = comment.css("svg::attr(class)").extract()
            rating = 0
            for itemClass in ratings:
                if itemClass == "shopee-svg-icon icon-rating-solid--active icon-rating-solid":
                    rating += 1
            item["rating"] = rating
            
            item["content"] = comment.css("div.shopee-product-rating__content ::text").extract_first()
            
            item["time"] = comment.css("div.shopee-product-rating__time ::text").extract_first()

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
                            "lua_source": self.pagination_script,
                            'viewport': '2573x2573',
                        },
                    },
                    'product_id': response.meta.get('product_id'),
                },
                dont_filter=True
            )
