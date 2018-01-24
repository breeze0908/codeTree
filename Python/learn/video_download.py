#! /usr/bin/python
# encoding=utf-8

import requests
import re
import urllib

def get_html(url):
        return requests.get(url).text


def get_div(doc):
        reg = re.compile(r'data-mp4=\"([^\"]+\.mp4)\"', re.S)
        return re.findall(reg, doc)

def download(url):
        filename = url.split('/')[-1:-2:-1]
        print './videos/'+filename[0]
        try:
                return urllib.urlretrieve(url, './videos/'+filename[0])
        except Exception,e:
                print "Error:",e
                return False

def main():
        start_url = "http://www.budejie.com/"
        html = get_html(start_url)
        videos = get_div(html)
        for i in videos:
                download(i)


if __name__ == '__main__':
        main()
