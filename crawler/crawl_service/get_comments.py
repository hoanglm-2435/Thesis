# -*- coding: utf-8 -*-
import requests
import time
import MySQLdb
import urllib3
from time import sleep
from time import localtime
from time import strftime
from collections import defaultdict

urllib3.disable_warnings()
conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'crawler_test',
            charset="utf8mb4",
            use_unicode=True,
        )
cursor = conn.cursor()

cursor.execute('''SELECT id, url, reviews FROM `{0}`;'''.format('products'))
start_urls = [(item[0], item[1], item[2]) for item in cursor.fetchall()]

# cursor.execute("""DROP TABLE IF EXISTS comments""")
cursor.execute("""create table if not exists comments(
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                author VARCHAR(512),
                rating INT,
                content TEXT,
                time TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY(author, time)
                ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci""")

user_agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1468.0 Safari/537.36'

for url in start_urls:
    url_item = list(url)

    product_id, shop_url, reviews = url_item

    item = shop_url.replace('.', ' ').split()
    shopid = item[len(item) - 2]
    itemid = item[len(item) - 1]
    print("productid: ", product_id, ",shopid: ", shopid,",itemid: ", itemid, ",reviews: ", reviews)

    offset = 0
    while (offset <= reviews) and (reviews > 0):
        params = {'offset': offset, 'itemid': itemid, 'shopid': shopid}
        url = "https://shopee.vn/api/v2/item/get_ratings?filter=0&flag=1&limit=6&type=0"
        response = requests.get(
                url, 
                params,
                timeout=5,
                verify=False,
                headers={
                'User-Agent': user_agent,
                },
            )
        sleep(5)

        data = response.json()

        comments = data.get('data').get('ratings')

        if len(comments) > 0:
            for comment in comments:
                content = comment.get('comment')
                if content != '' and content is not None:
                    item = defaultdict()
            
                    item['product_id'] = product_id
                    item['author'] = comment.get('author_username')
                    item['rating'] = comment.get('rating_star')
                    time_comment = comment.get('mtime')
                    item['time'] = strftime("%Y-%m-%d %H:%M:%S", localtime(time_comment))
                    item['content'] = content
                    
                    try:
                        cursor.execute("""INSERT INTO comments (product_id, author, rating, content, time) VALUES (%s, %s, %s, %s, %s)""", (
                                item['product_id'],
                                item['author'],
                                item['rating'],
                                item['content'],
                                item['time'],
                            ))
                        conn.commit()
                    except MySQLdb.Error as e:
                        print("Error %d: %s" % (e.args[0], e.args[1]))

                    print("Result: ", item['product_id'], item['author'], item['rating'], item['time'], item['content'])

            offset += 6
            sleep(5)
            print("Count: ", offset)
        else: 
            break