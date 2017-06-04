#!/bin/bash
cd /usr/local/src
wget http://openresty.org/download/ngx_openresty-1.7.2.1.tar.gz
tar xvf ngx_openresty-1.7.2.1.tar.gz 
cd ngx_openresty-1.7.2.1/bundle/
cd LuaJIT-2.1-20140707/
make clean
make && make install
cd ../
wget https://github.com/FRiCKLE/ngx_cache_purge/archive/2.3.tar.gz  #(wget ngx_cache_purge)
tar xvf 2.3.tar.gz
wget https://github.com/yaoweibin/nginx_upstream_check_module/archive/v0.3.0.tar.gz    #(wget nginx_upstream_check_module)
cd ../
./configure --prefix=/usr/local/ngx_openresty-1.7.2.1 --with-http_realip_module --with-pcre --with-luajit --add-module=./bundle/ngx_cache_purge-2.3/ --add-module=./bundle/nginx_upstream_check_module-0.3.0/ -j2 
make && make install