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
            'db_thesis',
            charset="utf8",
            use_unicode=True,
        )
        self.cursor = self.conn.cursor()

    def process_item(self, item, spider):
        try:
            if spider.name == 'products':
                self.cursor.execute("""INSERT INTO products (shop_id, url, name, price, stock, rating, reviews, sold, created_at) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)""", (
                    item['shop_id'],
                    item['url'],
                    item['name'],
                    item['price'],
                    item['stock'],
                    item['rating'],
                    item['reviews'],
                    item['sold'],
                    item['created_at'],
                ))
                self.conn.commit()

            elif spider.name == 'shopee_mall':
                self.cursor.execute("""INSERT INTO shopee_mall (cate_id, name, url) VALUES (%s, %s, %s)""", (
                    item['cate_id'],
                    item['name'],
                    item['url'],
                ))
                self.conn.commit()

            elif spider.name == 'shop_offline':
                self.cursor.execute("""INSERT INTO shop_offline (name, rating, location, phone_number) VALUES (%s, %s, %s, %s)""", (
                    item['name'],
                    item['rating'],
                    item['location'],
                    item['phone_number'],
                ))
                self.conn.commit()
            elif spider.name == 'shopee_category':
                self.cursor.execute("""INSERT INTO shopee_categories (cate_id, name, url) VALUES (%s, %s, %s)""", (
                    item['cate_id'],
                    item['name'],
                    item['url'],
                ))
                self.conn.commit()

        except MySQLdb.Error as e:
            print("Error:", e.args[0], e.args[1])
        return item
