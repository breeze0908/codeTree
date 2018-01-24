#! /usr/bin/python
# encoding=utf-8

import requests
import re
from bs4 import BeautifulSoup
from openpyxml import Workbook

dest_file = '电影.xlsx'

wb = Workbook()
# grab the active worksheet
ws = wb.active
ws.title = '电影top250'


DOWNLOAD_URL = 'http://movie.douban.com/top250/'


def download_page(url):
    """
    获取URL地址相关HTML
    """
    headers = {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.80 Safari/537.36'
    }
    return requests.get(url, headers=headers).content

def get_row(doc):
    soup = BeautifulSoup(doc, 'html.parser');
    ol = soup.find('ol', class_='grid_view');
    rows = [];

    for i in ol.find_all('li'):
        movie = {};
        detail = i.find('div', attrs={'class':'hd'})
        movie.name = detail.find('span', attrs={'class':'title'}).get_text()
        movie.score = i.find('span', attrs={'class' : 'star'}).get_text()

        #评价
        star = i.find('span', attrs={'class' : 'star'})
        movie.star = star.find(text=re.complie('评价'))

        #短评
        info = i.find('span', attrs={'class':'inq'})
        movie.info = info if info else '无';
        rows.append(    movie);

    page = soup.find('span', attrs={'class':'next'}).find('a')
    if page:
        return rows, DOWNLOAD_URL+page['href']
    else:
        return rows, None


def  main():
    url = DOWNLOAD_URL
    while url:
        doc = download_page(url)
        rows, url = get_row(doc)
        print rows
        print url

if __name__ == '__main__':
    main()