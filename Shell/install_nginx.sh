##########nginx安装#######################
cd /usr/local/src
#yum install -y pcre-devel zlib-devel
#wget http://nginx.org/download/nginx-1.8.0.tar.gz
#tar xvf nginx-1.8.0.tar.gz
cd nginx-1.8.0
./configure --prefix=/data/nginx-1.8.0 --without-http_memcached_module --with-http_ssl_module --with-http_gzip_static_module 
make
make install
#ln -s /usr/local/nginx-1.8.0/ /usr/local/nginx
/usr/local/nginx/sbin/nginx
#echo "/usr/local/nginx/sbin/nginx" >> /etc/rc.local
#################config nginx.conf###################
cpunum=`cat /proc/cpuinfo |grep processor |grep -v grep |wc -l`
nginx_processes=`expr $cpunum / 2`
sed -i 's/worker_processes * 1/worker_processes  '$nginx_processes'/'  /data/nginx-1.8.0/conf/nginx.conf
sed -i 's/worker_connections * 1024/worker_connections  10240/'  /data/nginx-1.8.0/conf/nginx.conf
#####################################################
################################################
