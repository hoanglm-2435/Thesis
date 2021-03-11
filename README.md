# Crawler

Dùng scrapy-splash kết hợp lua script để crawl

```
├── crawl_service
│   ├── __init__.py
│   ├── items.py
│   ├── middlewares.py
│   ├── pipelines.py
│   ├── run.py
│   ├── settings.py
│   └── spiders
│       ├── __init__.py
│       └── shopee.py
├── requirements.txt
└── scrapy.cfg
```

- Cài đặt Splash

Cài Docker sau đó chạy

```
$ sudo docker pull scrapinghub/splash
```

và

```
$ sudo docker run -p 8050:8050 scrapinghub/splash
```

- Cài các thư viện cần thiết khác ( Nên dùng virtualenv )

```
pip install -r requirements.txt
```

- Chạy command để crawl

```
scrapy crawl shopee
```
