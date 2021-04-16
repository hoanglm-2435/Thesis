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
            'crawler_test',
            charset="utf8",
            use_unicode=True,
        )
        self.cursor = self.conn.cursor()
        self.create_table()

    def create_table(self):
#         self.cursor.execute("""DROP TABLE IF EXISTS products""")
        self.cursor.execute("""create table if not exists products(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        shop_id INT NOT NULL,
                        url VARCHAR(512),
                        name VARCHAR(512),
                        price BIGINT,
                        stock INT,
                        sold INT,
                        rating DOUBLE,
                        reviews INT,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )""")

        # self.cursor.execute("""DROP TABLE IF EXISTS shopee_mall""")
        self.cursor.execute("""create table if not exists shopee_mall(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(512),
                        url VARCHAR(512),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY(name, url)
                        )""")

        # self.cursor.execute("""DROP TABLE IF EXISTS shop_offline""")
        self.cursor.execute("""create table if not exists shop_offline(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(512),
                        rating DOUBLE,
                        location VARCHAR(512),
                        phone_number VARCHAR(512),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE KEY(name, location)
                        )""")

    def process_item(self, item, spider):
        try:
            if spider.name == 'products':
                # self.cursor.execute("""alter table products ADD UNIQUE INDEX(id, name)""")
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
                # self.cursor.execute("""alter table shopee_mall ADD UNIQUE INDEX(name, url)""")
                self.cursor.execute("""INSERT INTO shopee_mall (name, url) VALUES (%s, %s)""", (
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

        except MySQLdb.Error as e:
            print("Error %d: %s" % (e.args[0], e.args[1]))
        return item
