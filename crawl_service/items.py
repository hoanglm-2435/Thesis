# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# https://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class OfficalShopItem(scrapy.Item):
    name = scrapy.Field()
    url = scrapy.Field()
    product_count = scrapy.Field()
    rate_average = scrapy.Field()
    follower = scrapy.Field()


class ProductItem(scrapy.Item):
    shop_id = scrapy.Field()
    url = scrapy.Field()
    name = scrapy.Field()
    price = scrapy.Field()
    stock = scrapy.Field()
    sold = scrapy.Field()
    reviews = scrapy.Field()
    rating = scrapy.Field()


class ShopeeMallItem(scrapy.Item):
    name = scrapy.Field()
    url = scrapy.Field()

class CommentItem(scrapy.Item):
    product_id = scrapy.Field()
    author = scrapy.Field()
    rating = scrapy.Field()
    content = scrapy.Field()
    time = scrapy.Field()
    