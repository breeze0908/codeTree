#! /usr/bin/env python
#-*-coding:utf-8-*-
import requests
import urllib
import re
import random
from time import sleep



def main():
    url=r'http://www.qingchen.date/article/82?query=2&a=1000'
    '''
    req = requests.get(url, verify=False, timeout=10)
    print req.text
    print req.status_code
    print req.headers
    print req.encoding
    '''

    '''
    i = 0;
    handler = urllib.urlopen(url)
    while i < 10:
        line = handler.readline();
        print line
        i += 1
    '''


    query = urllib.splitquery(url);
    print query

if __name__ == '__main__':
    main()