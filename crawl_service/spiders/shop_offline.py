# -*- coding: utf-8 -*-
import scrapy
from scrapy_splash import SplashRequest
from crawl_service.items import ShopOfflineItem

class ShopOfflineSpider(scrapy.Spider):
    name = "shop_offline"
    allowed_domains = ["google.com"]

    start_urls = ["https://www.google.com/search?q=shop+sneaker+h%C3%A0+n%E1%BB%99i&gbv=2&biw=580&bih=667&tbm=lcl&sxsrf=ALeKk03riuE9SHOflf3ictArwvZedmBfGg%3A1618407941145&ei=BfJ2YI-zCInv-QaT1p_4BQ&oq=shop+sneaker+h%C3%A0+n%E1%BB%99i&gs_l=psy-ab.3...0.0.0.394459.0.0.0.0.0.0.0.0..0.0....0...1c..64.psy-ab..0.0.0....0.bIgkdAEaivQ#rlfi=hd:;si:;mv:[[21.0347708,105.866964],[20.9973767,105.79632199999999]];tbs:lrf:!1m4!1u3!2m2!3m1!1e1!1m4!1u2!2m2!2m1!1e1!2m1!1e2!2m1!1e3!3sIAE,lf:1,lf_ui:10"]

    script = '''
    function main(splash)
        assert(splash:go(splash.args.url))
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
                    'wait': 10,
                    'viewport': '3964x3964',
                },
                dont_filter=True
            )

    def parse(self, response):
        next_url = response.css('a#pnnext::attr(href)').extract_first()

        item = ShopOfflineItem()
        for data in response.css("div.uMdZh.tIxNaf.mnr-c"):
            shopName = data.css(
                "div.dbg0pd > div ::text").extract()
            item['name'] = ''.join(shopName)

            rating = data.css(
                "span.BTtC6e ::text").extract_first() or '0'
            item['rating'] = float(rating.replace(',', '.'))
            
            info = data.css(
                "div.uMdZh.tIxNaf.mnr-c > div > a > div > span > div:nth-child(2) ::text").extract_first()
            sliceInfo = info.find('·')
            if sliceInfo != -1:
                item["location"] = info[0:(sliceInfo-1)]
                item["phone_number"] = info[(sliceInfo+2)::]
            else:
                item["location"] = info
                item["phone_number"] = None

            yield item

        if next_url is not None:
            yield SplashRequest(
                url='https://www.google.com' + next_url,
                callback=self.parse,
                meta={
                    "splash": {
                        "endpoint": "execute",
                        "args": {
                            'wait': 5,
                            'url': 'https://www.google.com' + next_url,
                            "lua_source": self.script,
                        },
                    }
                },
                dont_filter=True
            )
