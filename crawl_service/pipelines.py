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
                        shop_id INT NOT NULL,
                        url VARCHAR(512),
                        name VARCHAR(512),
                        price BIGINT,
                        stock INT,
                        sold INT,
                        rating DOUBLE,
                        reviews INT,
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

        self.cursor.execute("""DROP TABLE IF EXISTS comments""")
        self.cursor.execute("""create table if not exists comments(
                        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        product_id INT NOT NULL,
                        author VARCHAR(512),
                        rating INT,
                        content TEXT,
                        time TIMESTAMP
                        )""")

    def process_item(self, item, spider):
        try:
            if spider.name == 'products':
                # self.cursor.execute("""alter table products ADD UNIQUE INDEX(id, name)""")
                self.cursor.execute("""INSERT INTO products (shop_id, url, name, price, stock, rating, reviews, sold) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)""", (
                    item['shop_id'],
                    item['url'],
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

            elif spider.name == 'comments':
                self.cursor.execute("""INSERT INTO comments (product_id, author, rating, content, time) VALUES (%s, %s, %s, %s, %s)""", (
                    item['product_id'],
                    item['author'],
                    item['rating'],
                    item['content'],
                    item['time'],
                ))
                self.conn.commit()

        except MySQLdb.Error as e:
            print("Error %d: %s" % (e.args[0], e.args[1]))
        return item
