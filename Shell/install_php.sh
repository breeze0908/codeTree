cd /usr/local/src
version=5.6.14
#yum install -y freetype-devel libpng-devel libjpeg-devel libxml2-devel bzip2 bzip2-devel libcurl libcurl-devel readline-devel
wget  ftp://mcrypt.hellug.gr/pub/crypto/mcrypt/libmcrypt/libmcrypt-2.5.7.tar.gz
tar xzvf libmcrypt-2.5.7.tar.gz
cd libmcrypt-2.5.7
./configure --prefix=/usr/local/libmcrypt/
make && make install

cd /usr/local/src
wget http://ncu.dl.sourceforge.net/project/mhash/mhash/0.9.9.9/mhash-0.9.9.9.tar.bz2
tar xjvf mhash-0.9.9.9.tar.bz2
cd mhash-0.9.9.9
echo "/usr/local/lib/" >>/etc/ld.so.conf
ldconfig -v
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
version=5.6.14
cd /usr/local/src
#5.3.8用这个下载
#wget http://museum.php.net/php5/php-${version}.tar.gz
#wget http://cn2.php.net/distributions/php-5.6.14.tar.gz
#chattr -i /etc/passwd
#chattr -i /etc/shadow
#useradd www 
#chattr +i /etc/passwd
#chattr +i /etc/shadow
#tar zxvf php-${version}.tar.gz
cd php-${version}
##./configure --prefix=/usr/local/php5.6.12 --with-mysql --with-mysqli --enable-fpm --with-zlib --with-gd
#./configure --prefix=/usr/local/php-${version} --with-mysql --with-mysqli --enable-fpm --with-zlib --with-gd --with-png-dir --with-jpeg-dir  --with-freetype-dir --with-iconv --enable-mbstring=cn --with-mysql=mysqlnd --enable-sockets  --enable-fpm
#./configure --prefix=/usr/local/php --with-config-file-path=/usr/local/php/etc --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-opcache --enable-fpm --with-fpm-user=www --with-fpm-group=www --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gettext --enable-mbstring --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem  --enable-sysvshm  --enable-sockets --with-curl --with-zlib --enable-zip  --with-bz2 --with-readline --with-png-dir --with-jpeg-dir --with-freetype-dir --with-gd 
./configure --prefix=/data/php-5.6.14 --with-config-file-path=/data/php-5.6.14/etc --enable-inline-optimization --disable-debug --disable-rpath --enable-shared --enable-opcache --enable-fpm --with-fpm-user=nobody --with-fpm-group=nobody --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-gettext --enable-mbstring --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem  --enable-sysvshm  --enable-sockets --with-curl --with-zlib --enable-zip  --with-bz2 --with-readline --with-png-dir --with-jpeg-dir --with-freetype-dir --with-gd --with-libmcrypt=/usr/local/libmcrypt --with-mhash=/usr/local/mhash/ --with-mcrypt-dir=/usr/local/mcrypt 
  
#这些选项倒是常用,gd得装那3个包才行 --with-mysqli=/usr/local/mysql/bin/mysql_config  gd的那些with libjpeg freetype全部要with才可以>，dir可以空

#make ZEND_EXTRA_LIBS='-liconv'
make && make install

if [ $? -eq 0 ]; then
#	ln -s /usr/local/php-5.6.14 /usr/local/php
	cp ./php.ini-production /usr/local/php/etc/php.ini
	cp /data/php-5.6.14/etc/php-fpm.conf.default /usr/local/php/etc/php-fpm.conf
	#5.3.8要取消两行注释
	#sed -i s/';pm.min_spare_servers'/'pm.min_spare_servers'/ /usr/local/php/etc/php-fpm.conf
	#sed -i s/';pm.max_spare_servers'/'pm.max_spare_servers'/ /usr/local/php/etc/php-fpm.conf
	/usr/local/php/sbin/php-fpm
	echo "" >> /etc/rc.local
	echo "/usr/local/php/sbin/php-fpm -c /usr/local/php/etc/php.ini -y /usr/local/php/etc/php-fpm.conf" >> /etc/rc.local
fi
