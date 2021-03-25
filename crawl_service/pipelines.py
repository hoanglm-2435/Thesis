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
        self.create_table()
        
    def create_table(self):
        # self.cursor.execute("""DROP TABLE IF EXISTS products""")
        self.cursor.execute("""create table if not exists products(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(512),
                        price VARCHAR(512),
                        stock VARCHAR(512),
                        sold VARCHAR(512),
                        rating VARCHAR(512),
                        reviews VARCHAR(512),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        )""")
        
        # self.cursor.execute("""DROP TABLE IF EXISTS shopee_mall""")
        self.cursor.execute("""create table if not exists shopee_mall(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(512),
                        url VARCHAR(512),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        UNIQUE KEY(name, url)
                        )""")

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
                # self.cursor.execute("""alter table products ADD UNIQUE INDEX(id, name)""")
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
                # self.cursor.execute("""alter table shopee_mall ADD UNIQUE INDEX(name, url)""")
                self.cursor.execute("""INSERT INTO shopee_mall (name, url) VALUES (%s, %s)""", (
                    item['name'],
                    item['url'],
                ))
                self.conn.commit()

        except MySQLdb.Error as e:
            print("Error %d: %s" % (e.args[0], e.args[1]))
        return item
