#! /usr/bin/python
# encoding=utf-8

"""
@desc 下载momentum中的精美壁纸
@api https://d3cbihxaqsuq0s.cloudfront.net/
@author Tan <tandamailzone@gmail.com>
"""

import requests
import re
import os
import urllib
import xml.dom.minidom

def download(url, path = ''):
    name    = url.split('/')[-1:-2:-1]
    path    = path if path else os.path.join('./tmp/', fname if fname else name)
    dirname = os.path.dirname(path)
    print path
    if not os.path.exists(dirname):
         os.makedirs(dirname)
    try:
        return urllib.urlretrieve(url, path)
    except Exception,e:
        print "Error:",e
        return False


def parseXMLFile(name):
    DOM  = xml.dom.minidom.parse(name)
    List = DOM.documentElement
    if List.hasAttribute("xmlns"):
        print "ListBucketResult  : %s" % List.getAttribute("xmlns")

    results = []
    # 在集合中获取所有Contents
    contents = List.getElementsByTagName("Contents")
    # 打印每个content的详细信息
    for c in contents:
        print "*****Content*****"
        Key = c.getElementsByTagName('Key')[0]
        #print "Key: %s" % Key.childNodes[0].data
        LastModified = c.getElementsByTagName('LastModified')[0]
        #print "LastModified: %s" % LastModified.childNodes[0].data
        ETag = c.getElementsByTagName('ETag')[0]
        #print "ETag: %s" % ETag.childNodes[0].data
        Size = c.getElementsByTagName('Size')[0]
        #print "Size: %s" % Size.childNodes[0].data
        StorageClass = c.getElementsByTagName('StorageClass')[0]
        #print "StorageClass: %s" % StorageClass.childNodes[0].data
        item = {
            "key"  : Key.childNodes[0].data,
            "etag" : ETag.childNodes[0].data,
            "size" : Size.childNodes[0].data,
            "lastModified" : LastModified.childNodes[0].data,
            "storageClass" : StorageClass.childNodes[0].data
        }
        results.append(item)
    return results


def main():
    tmp = './tmp/';
    #下载xml
    path = os.path.join(tmp, 'contents.xml')
    if not download('https://d3cbihxaqsuq0s.cloudfront.net/', path):
        return False
    #解析xml并下载图片
    contents = parseXMLFile(path)
    if len(contents):
        for item in contents:
            print item
            path = os.path.join(tmp, item['key'])
            if not item['key'].find('.jpg'):
                continue
            if os.path.exists(path):
                continue
            download('https://d3cbihxaqsuq0s.cloudfront.net/' + item['key'], path)
    return True

if __name__ == '__main__':
    main()



