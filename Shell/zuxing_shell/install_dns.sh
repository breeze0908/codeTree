#!/bin/bash

cd /usr/local/src
wget ftp://ftp.isc.org/isc/bind9/9.6.1/bind-9.6.1.tar.gz
tar xvf bind-9.6.1.tar.gz
./configure --prefix=/usr/local/bind-9.6.1 --enable-largefile --enable-threads
make && make install
cd /usr/local/
ln -s bind-9.6.1 bind
