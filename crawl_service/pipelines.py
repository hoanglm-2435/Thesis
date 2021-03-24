# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: https://doc.scrapy.org/en/latest/topics/item-pipeline.html
import sys
import MySQLdb
import hashlib
from scrapy.exceptions import DropItem
from scrapy.http import Request
from crawl_service.items import *


class CrawlServicePipeline(object):
    def __init__(self):
        self.conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'shopee_crawler',
            charset="utf8",
            use_unicode=True,
        )
        self.cursor = self.conn.cursor()

    def process_item(self, item, spider):
        try:
            if spider.name == 'offical_shop':
                self.cursor.execute("""INSERT INTO shops (name, url, product_count, rate_average, follower) VALUES (%s, %s, %s, %s, %s)""", (
                    item['name'],
                    item['url'],
                    item['product_count'],
                    item['rate_average'],
                    item['follower'],
                ))
                self.conn.commit()
            elif spider.name == 'products':
                self.cursor.execute("""INSERT INTO products (name, price, stock, rating, reviews, sold) VALUES (%s, %s, %s, %s, %s, %s)""", (
                    item['name'],
                    item['price'],
                    item['stock'],
                    item['rating'],
                    item['reviews'],
                    item['sold'],
                ))
                self.conn.commit()
            elif spider.name == 'shopee_mall':
                self.cursor.execute("""INSERT INTO shopee_mall (name, url) VALUES (%s, %s)""", (
                    item['name'],
                    item['url'],
                ))
                self.conn.commit()

        except MySQLdb.Error as e:
            print("Error %d: %s" % (e.args[0], e.args[1]))
        return item
