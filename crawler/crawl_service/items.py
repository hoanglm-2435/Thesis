# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# https://doc.scrapy.org/en/latest/topics/items.html

import scrapy

class ProductItem(scrapy.Item):
    shop_id = scrapy.Field()
    url = scrapy.Field()
    name = scrapy.Field()
    price = scrapy.Field()
    stock = scrapy.Field()
    sold = scrapy.Field()
    reviews = scrapy.Field()
    rating = scrapy.Field()
    created_at = scrapy.Field()

class ShopeeMallItem(scrapy.Item):
    name = scrapy.Field()
    url = scrapy.Field()

class CommentItem(scrapy.Item):
    product_id = scrapy.Field()
    author = scrapy.Field()
    rating = scrapy.Field()
    content = scrapy.Field()
    time = scrapy.Field()
    
class ShopOfflineItem(scrapy.Item):
    name = scrapy.Field()
    rating = scrapy.Field()
    location = scrapy.Field()
    phone_number = scrapy.Field()