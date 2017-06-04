#!/bin/bash
if [ ! -d /usr/local/src/ ];then
        mkdir /usr/local/src
fi

cd /usr/local/src
wget http://www.apache.org/dist/apr/apr-1.5.2.tar.bz2
wget http://archive.apache.org/dist/apr/apr-util-1.5.4.tar.bz2
wget http://sourceforge.net/projects/pcre/files/pcre/8.34/pcre-8.34.tar.gz
wget http://archive.apache.org/dist/httpd/httpd-2.2.19.tar.gz


tar xvf apr-1.5.2.tar.gz
cd apr-1.5.2
./configure --prefix=/usr/local/apr
make && make install

cd /usr/local/src
tar xvf apr-util-1.5.4.tar.gz
cd apr-util-1.5.4
./configure --prefix=/usr/local/apr-util --with-apr=/usr/local/apr
make && make install

cd /usr/local/src
tar xvf pcre-8.34.tar.gz
cd pcre-8.34
./configure --prefix=/usr/local/pcre
make && make install

cd /usr/local/src
tar xvf httpd-2.4.17.tar.gz
cd httpd-2.4.17
./configure --prefix=/usr/local/apache-2.4.17 --enable-so --enable-rewrite --with-apr=/usr/local/apr --with-apr-util=/usr/local/apr-util/ --with-pcre=/usr/local/pcre
make && make install
