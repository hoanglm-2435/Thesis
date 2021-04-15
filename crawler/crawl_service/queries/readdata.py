import MySQLdb
from itemadapter import ItemAdapter


def dataReader(spiderName):
    try:
        conn = MySQLdb.connect(
            'localhost',
            'root',
            'hoangminh99',
            'crawler_test',
            charset="utf8",
            use_unicode=True,
        )
        cursor = conn.cursor()
        cursor.execute('''SELECT id, url FROM `{0}`;'''.format(spiderName))
        list = [(item[0], item[1]) for item in cursor.fetchall()]

        return dict(list)
    except MySQLdb.Error as e:
        print("Error reading data from MySQL table", e)
