import requests
import time
import MySQLdb
import urllib3
from time import sleep
from time import localtime
from time import strftime
from crawl_service.queries.readdata import getProducts
from crawl_service.items import CommentItem

urllib3.disable_warnings()
conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'shopee_crawler',
            charset="utf8mb4",
            use_unicode=True,
        )
cursor = conn.cursor()
# cursor.execute("""DROP TABLE IF EXISTS comments""")
cursor.execute("""create table if not exists comments(
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                product_id INT NOT NULL,
                author VARCHAR(512),
                rating INT,
                content TEXT,
                time TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY(product_id, time)
                ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci""")

user_agent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1468.0 Safari/537.36'
start_urls = getProducts(spiderName='products')
# start_urls = [(4815, 'https://shopee.vn/-Mã-FASHIONMALLT4-giảm-15-tối-đa-30K-đơn-150k-Bóp-nam-Huy-Hoàng-da-bò-Nâu-HP2103-Đen-HP2102-Da-HP2101-i.28700851.397388306', 895), (4816, 'https://shopee.vn/-Mã-FASHIONMALLT4-giảm-15-tối-đa-30K-đơn-150k-Giày-nam-tăng-chiều-cao-Huy-Hoàng-màu-đen-HP7189-i.28700851.397405575', 126), (4817, 'https://shopee.vn/-Mã-FASHIONMALLT4-giảm-15-tối-đa-30K-đơn-150k-Dây-nịt-nam-da-bò-Huy-Hoàng-đầu-kim-Trắng-HP4107-Đen-HP4108-Nâu-HP4109-i.28700851.430668153', 835), (4818, 'https://shopee.vn/Giày-sabo-nam-Huy-Hoàng-da-bò-màu-đen-HP7127-i.28700851.486841774', 29)]

for url in start_urls:
    url_item = list(url)
    product_id, shop_url, reviews = url_item

    item = shop_url.replace('.', ' ').split()
    shopid = item[len(item) - 2]
    itemid = item[len(item) - 1]
    print("productid: ", product_id, ",shopid: ", shopid,",itemid: ", itemid, ",reviews: ", reviews)

    offset = 0
    while (offset <= reviews) and (reviews > 0):
        try: 
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
            sleep(10)

            data = response.json()

            comments = data.get('data').get('ratings')

            if len(comments) > 0:
                for comment in comments:
                    content = comment.get('comment')
                    if content != '' and content is not None:
                        item = CommentItem()
                
                        item['product_id'] = product_id
                        item['author'] = comment.get('author_username')
                        item['rating'] = comment.get('rating_star')
                        # image_id = comment.get('images')
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
                print("Count: ", offset)
            else: 
                break
            sleep(5)
        except:
            time.sleep(10)
            continue