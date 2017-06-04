yum -y install gcc
cd /usr/local/src
wget http://www.monkey.org/~provos/libevent-2.0.12-stable.tar.gz
tar zxf libevent-2.0.12-stable.tar.gz
cd libevent-2.0.12-stable
./configure  --prefix=/usr/local/lib
 make && make install

cd /usr/local/src
wget http://memcached.org/files/memcached-1.4.20.tar.gz
tar xvf memcached-1.4.20.tar.gz
cd memcached-1.4.20
./configure --prefix=/usr/local/memcached --with-libevent=/usr/local/lib && make && make install
/usr/local/memcached/bin/memcached -p 12677 -U 0 -d -r -u root -m 2040 -c 1024 -t 4

echo "/usr/local/memcached/bin/memcached -p 12677 -U 0 -d -r -u root -m 2040 -c 1024 -t 4"  >> /etc/rc.local