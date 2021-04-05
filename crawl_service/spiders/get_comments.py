import requests
import MySQLdb
import time
from time import sleep
from time import localtime
from time import strftime
from crawl_service.items import CommentItem
from crawl_service.queries.readdata import getProducts

conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'shopee_crawler',
            charset="utf8",
            use_unicode=True,
        )
cursor = conn.cursor()
cursor.execute("""DROP TABLE IF EXISTS comments""")
cursor.execute("""create table if not exists comments(
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                author VARCHAR(512),
                rating INT,
                content TEXT,
                time TIMESTAMP
                )""")

user_agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1468.0 Safari/537.36'
start_urls = getProducts(spiderName='products')

for url in start_urls:
    url_item = list(url)
    product_id, shop_url, reviews = url_item

    item = shop_url[-19:]
    shopid = item[:8]
    itemid = item[9:]
    print(shopid, itemid, reviews)

    params = {'limit': 59, 'itemid': itemid, 'shopid': shopid}
    url = "https://shopee.vn/api/v2/item/get_ratings?filter=0&flag=1&offset=0&type=0"
    response = requests.get(url, params, headers={
        'User-Agent': user_agent,
    })
    sleep(10)

    data = response.json()

    comments = data.get('data').get('ratings')

    for comment in comments:
        item = CommentItem()
        
        item['product_id'] = product_id
        item['author'] = comment.get('author_username')
        item['rating'] = comment.get('rating_star')
        # image_id = comment.get('images')
        time_comment = comment.get('mtime')
        item['time'] = strftime("%Y-%m-%d %H:%M:%S", localtime(time_comment))
        item['content'] = comment.get('comment')
        
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

    print("Count: ", len(comments))