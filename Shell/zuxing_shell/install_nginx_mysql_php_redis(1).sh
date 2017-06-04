#!/bin/bash
yum install wget gcc gcc-c++ ncurses* openssl openssl-devel -y

if [ ! -d /usr/local/src/ ];then
        mkdir /usr/local/src
fi

########安装mysql###########
cd /usr/local/src
#wget  http://www.cmake.org/files/v2.8/cmake-2.8.3.tar.gz
wget  ftp://121.201.104.71/sandai_caizuxing/cmake-2.8.3.tar.gz
tar xvf cmake-2.8.3.tar.gz
cd cmake-2.8.3
./configure --prefix=/usr/local/cmake
make && make install

cd /usr/local/src
#wget   http://sourceforge.net/projects/boost/files/boost/1.59.0/boost_1_59_0.tar.gz
 wget  --ftp-user=ftpuser --ftp-password=ftpuser.2016   ftp://121.201.104.71/boost_1_59_0.tar.gz
tar xvf boost_1_59_0.tar.gz -C /usr/local/
mv /usr/local/boost_1_59_0 /usr/local/boost
chattr -i /etc/passwd
chattr -i /etc/shadow
useradd mysql
chattr +i /etc/passwd
chattr +i /etc/shadow
#wget http://cdn.mysql.com/Downloads/MySQL-5.7/mysql-5.7.9.tar.gz
wget  --ftp-user=ftpuser --ftp-password=ftpuser.2016   ftp://121.201.104.71/mysql-5.7.9.tar.gz
tar xvf mysql-5.7.9.tar.gz
cd mysql-5.7.9
#/usr/local/cmake/bin/cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql-5.7.9 
#/usr/local/cmake/bin/cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql-5.7.9 -DWITH_INNOBASE_STORAGE_ENGINE=1 -DWITH_MYISAM_STORAGE_ENGINE=1 -DENABLED_LOCAL_INFILE=1   -DWITH_DEBUG=0 -DWITH_BOOST=/usr/local/boost
/usr/local/cmake/bin/cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql-5.7.9 -DMYSQL_DATADIR=/usr/local/mysql-5.7.9/data -DWITH_INNOBASE_STORAGE_ENGINE=1  -DWITH_EMBEDDED_SERVER=OFF  -DWITH_BOOST=/usr/local/boost
make && make install
mkdir /usr/local/mysql-5.7.9/run 
chown mysql:mysql -R /usr/local/mysql-5.7.9
ln -s /usr/local/mysql-5.7.9 /usr/local/mysql
mv /etc/my.cnf /etc/my.cnf.bak
cp /usr/local/mysql-5.7.9/support-files/my-default.cnf /etc/my.cnf
cp /usr/local/mysql-5.7.9/support-files/mysql.server /etc/init.d/mysqld 
chmod a+x /etc/init.d/mysqld
chkconfig mysqld on

#############################my.cnf###############################
sed -i "s/# datadir =.*/datadir = \/usr\/local\/mysql-5.7.9\/data /g" /etc/my.cnf
sed -i "s/^# basedir =.*/basedir = \/usr\/local\/mysql-5.7.9 /g" /etc/my.cnf
sed -i "s/^# port =.*/port = 3306 /g" /etc/my.cnf

#/usr/local/mysql-5.7.9/bin/mysqld --initialize --user=mysql --basedir=/usr/local/mysql-5.7.9/ --datadir=/usr/local/mysql-5.7.9/data/
#######修改mysql配置文件############
mkdir /usr/local/mysql-5.7.9/logs
chown mysql:mysql /usr/local/mysql-5.7.9/logs

#alter user 'root'@'localhost' identified by 'sd-9898w';


########安装redis###########
cd /usr/local/src
wget  http://download.redis.io/releases/redis-3.0.5.tar.gz
tar xvf redis-3.0.5.tar.gz
mv redis-3.0.5 /usr/local/
cd /usr/local/redis-3.0.5/
make
mkdir data  logs 
cp /usr/local/redis-3.0.5/redis.conf /etc/redis.conf
echo "vm.overcommit_memory = 1" >>/etc/sysctl.conf
sysctl vm.overcommit_memory=1
sed -i "s/daemonize no/daemonize yes/" /etc/redis.conf
sed -i "/logfile/d" /etc/redis.conf
sed -i "/daemonize yes/a logfile \/usr\/local\/redis-3.0.5\/logs\/redis.log" /etc/redis.conf
sed -i "s/# syslog-enabled no/syslog-enabled no/" /etc/redis.conf
sed -i "s/dir .\//dir \/usr\/local\/redis-3.0.5\/data/" /etc/redis.conf
/usr/local/redis-3.0.5/src/redis-server /etc/redis.conf &
echo -e "\n"  >> /etc/rc.local
echo "/usr/local/redis-3.0.5/src/redis-server /etc/redis.conf &" >>/etc/rc.local
#./redis-cli -p 7030 shutdown  关闭redis


########安装sphinx###########
cd /usr/local/src
yum install automake libtool mysql-devel* -y
wget http://www.coreseek.cn/uploads/csft/4.0/coreseek-4.1-beta.tar.gz
tar xvf coreseek-4.1-beta.tar.gz
cd  coreseek-4.1-beta/mmseg-3.2.14/
./configure --prefix=/usr/local/mmseg-3.2.14
aclocal
libtoolize --force
automake --add-missing
autoconf
autoheader
make clean
make && make install

cd /usr/local/src
cd coreseek-4.1-beta/csft-4.1/
sh buildconf.sh
./configure --prefix=/usr/local/coreseek-4.1 --without-unixodbc --with-mmseg --with-mmseg-includes=/usr/local/mmseg-3.2.14/include/mmseg/ --with-mmseg-libs=/usr/local/mmseg-3.2.14/lib/ --with-mysql
######不需要支持mysql的编译####################
#./configure --prefix=/usr/local/coreseek-4.1 --without-mysql --without-unixodbc --with-mmseg --with-mmseg-includes=/usr/local/mmseg-3.2.14/include/mmseg/ --with-mmseg-libs=/usr/local/mmseg-3.2.14/lib/
aclocal
libtoolize --force
automake --add-missing
autoconf
autoheader
make clean
make && make install

##########nginx安装#######################
cd /usr/local/src
yum install -y pcre-devel zlib-devel
wget http://nginx.org/download/nginx-1.8.0.tar.gz
tar xvf nginx-1.8.0.tar.gz
cd nginx-1.8.0
./configure --prefix=/usr/local/nginx-1.8.0 --without-http_memcached_module --with-http_ssl_module --with-http_gzip_static_module 
make
make install
ln -s /usr/local/nginx-1.8.0/ /usr/local/nginx
/usr/local/nginx/sbin/nginx
echo "/usr/local/nginx/sbin/nginx" >> /etc/rc.local
#################config nginx.conf###################
cpunum=`cat /proc/cpuinfo |grep processor |grep -v grep |wc -l`
nginx_processes=`expr $cpunum / 2`
sed -i 's/worker_processes * 1/worker_processes  '$nginx_processes'/'  /usr/local/nginx-1.8.0/conf/nginx.conf
sed -i 's/worker_connections * 1024/worker_connections  10240/'  /usr/local/nginx-1.8.0/conf/nginx.conf
#####################################################
#################config iptablesl####################
sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
sed -i "/COMMIT/d" /etc/sysconfig/iptables
echo '-A RH-Firewall-1-INPUT -m state --state NEW -m tcp -p tcp --dport 80 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
service iptables restart
################################################

####################php安装###########################################
cd /usr/local/src
version=5.6.14
yum install -y freetype-devel libpng-devel libjpeg-devel libxml2-devel bzip2 bzip2-devel libcurl libcurl-devel readline-devel
wget  ftp://mcrypt.hellug.gr/pub/crypto/mcrypt/libmcrypt/libmcrypt-2.5.7.tar.gz
tar xzvf libmcrypt-2.5.7.tar.gz
cd libmcrypt-2.5.7
./configure --prefix=/usr/local/libmcrypt/
make && make install

cd /usr/local/src
wget http://ncu.dl.sourceforge.net/project/mhash/mhash/0.9.9.9/mhash-0.9.9.9.tar.bz2
tar xjvf mhash-0.9.9.9.tar.bz2
cd mhash-0.9.9.9
#echo "/usr/local/lib/" >>/etc/ld.so.conf
#ldconfig -v
./configure --prefix=/usr/local/mhash
make && make install

cd /usr/local/src
wget http://ncu.dl.sourceforge.net/project/mcrypt/MCrypt/2.6.8/mcrypt-2.6.8.tar.gz
tar xvf mcrypt-2.6.8.tar.gz 
cd mcrypt-2.6.8
export LD_LIBRARY_PATH=/usr/local/libmcrypt/lib:/usr/local/mhash/lib
export LDFLAGS="-L/usr/local/mhash/lib -I/usr/local/mhash/include/"
export CFLAGS="-I/usr/local/mhash/include/"
./configure --prefix=/usr/local/mcrypt --with-libmcrypt-prefix=/usr/local/libmcrypt
make && make install

###############要再安装一次，要不然会报错：找不到mcrypt.h文件#############
cd /usr/local/src
cd libmcrypt-2.5.7
./configure 
make && make install
###############因为在上面的步骤中已经echo "/usr/local/lib/" >>/etc/ld.so.conf，ldconfig -v解决--enable-opcache=no这个错误#####################
ldconfig -v 

cd /usr/local/src
#5.3.8用这个下载
#wget http://museum.php.net/php5/php-${version}.tar.gz
wget http://cn2.php.net/distributions/php-5.6.14.tar.gz
#chattr -i /etc/passwd
#chattr -i /etc/shadow
#useradd www 
#chattr +i /etc/passwd
#chattr +i /etc/shadow
tar zxvf php-${version}.tar.gz
cd php-${version}
##./configure --prefix=/usr/local/php5.6.12 --with-mysql --with-mysqli --enable-fpm --with-zlib --with-gd
#./configure --prefix=/usr/local/php-${version} --with-mysql --with-mysqli --enable-fpm --with-zlib --with-gd --with-png-dir --with-jpeg-dir  --with-freetype-dir --with-iconv --enable-mbstring=cn --with-mysql=mysqlnd --enable-sockets  --enable-fpm
#./configure --prefix=/usr/local/php --with-config-file-path=/usr/local/php/etc --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-opcache --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gettext --enable-mbstring --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem  --enable-sysvshm  --enable-sockets --with-curl --with-zlib --enable-zip  --with-bz2 --with-readline --with-png-dir --with-jpeg-dir --with-freetype-dir --with-gd 
./configure --prefix=/usr/local/php-5.6.14 --with-config-file-path=/usr/local/php-5.6.14/etc --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-opcache --enable-fpm --with-fpm-user=nobody --with-fpm-group=nobody --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gettext --enable-mbstring --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem  --enable-sysvshm  --enable-sockets --with-curl --with-zlib --enable-zip  --with-bz2 --with-readline --with-png-dir --with-jpeg-dir --with-freetype-dir --with-gd --with-libmcrypt=/usr/local/libmcrypt --with-mhash=/usr/local/mhash/ --with-mcrypt-dir=/usr/local/mcrypt 
  
#这些选项倒是常用,gd得装那3个包才行 --with-mysqli=/usr/local/mysql/bin/mysql_config  gd的那些with libjpeg freetype全部要with才可以>，dir可以空

#make ZEND_EXTRA_LIBS='-liconv'
make && make install

if [ $? -eq 0 ]; then
	ln -s /usr/local/php-5.6.14 /usr/local/php
	cp ./php.ini-production /usr/local/php/etc/php.ini
	cp /usr/local/php-5.6.14/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
	#5.3.8要取消两行注释
	#sed -i s/';pm.min_spare_servers'/'pm.min_spare_servers'/ /usr/local/php/etc/php-fpm.conf
	#sed -i s/';pm.max_spare_servers'/'pm.max_spare_servers'/ /usr/local/php/etc/php-fpm.conf
	/usr/local/php/sbin/php-fpm
	echo "" >> /etc/rc.local
	echo "/usr/local/php/sbin/php-fpm -c /usr/local/php/etc/php.ini -y /usr/local/php/etc/php-fpm.conf" >> /etc/rc.local
fi



#####redis扩展#################
cd /usr/local/src
yum install git -y
git clone https://github.com/phpredis/phpredis.git
cd phpredis/
/usr/local/php/bin/phpize
./configure --with-php-config=/usr/local/php/bin/php-config
make && make install

#####sphinx扩展#################
cd /usr/local/src
yum install automake libtool mysql-devel* -y
wget http://www.coreseek.cn/uploads/csft/4.0/coreseek-4.1-beta.tar.gz
tar xvf coreseek-4.1-beta.tar.gz
cd /usr/local/src/coreseek-4.1-beta/csft-4.1/api/libsphinxclient/
./configure --prefix=/usr/local/libsphinxclient
aclocal
libtoolize --force
automake --add-missing
autoconf
autoheader
make clean
make && make install

cd /usr/local/src
wget http://pecl.php.net/get/sphinx-1.3.3.tgz
tar xvf sphinx-1.3.3.tgz
cd sphinx-1.3.3
/usr/local/php/bin/phpize
./configure --with-php-config=/usr/local/php/bin/php-config --with-sphinx=/usr/local/libsphinxclient
make && make install


##########添加扩展到php.ini文件###############
sed -i '/user_dir/a extension_dir = "/usr/local/php-5.6.14/lib/php/extensions/no-debug-non-zts-20131226/" \nextension = redis.so \nextension = sphinx.so' /usr/local/php/etc/php.ini
killall php-fpm
/usr/local/php/sbin/php-fpm






