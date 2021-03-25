import MySQLdb
from itemadapter import ItemAdapter

def dataReader(spiderName):
    try:
        conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'shopee_crawler',
            charset="utf8",
            use_unicode=True,
        )
        cursor = conn.cursor()
        cursor.execute('''SELECT url FROM `{0}`;'''.format(spiderName))
        list = [item[0] for item in cursor.fetchall()]
        return list
    except MySQLdb.Error as e:
        print("Error reading data from MySQL table", e)