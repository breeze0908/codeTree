#!/bin/bash

software="mysql, PHP, nginx, redis,redis extension,mongodb extension,memcache extensionnode, python, memcache, rsync, FTP，mongodb"
echo -e "Instruction For Use:
	No version to choose, the online servers are using this script version.
	You can choose to install\033[31m $software \033[0msoftware.You can also use this script to install\033[31m multiple redis instance\033[0m.
	Mysql version is 5.7.9,nginx version is 1.8.0,php version is 5.6.14,python verison is 2.7.6,node version is 4.3.1,redis version is 3.0.5 and memcache version is 1.4.20.
	If you need to install other versions, you can contact caizuxing through RTX.Thank you!"


Install_mysql()
{
    cd $src_wget_dir
#    wget  http://www.cmake.org/files/v2.8/cmake-2.8.3.tar.gz
	wget	ftp://121.201.104.71/sandai_caizuxing/cmake-2.8.3.tar.gz
    tar xvf cmake-2.8.3.tar.gz
    cd cmake-2.8.3
    ./configure --prefix=/usr/local/cmake
    make && make install

    cd $src_wget_dir
    #wget   http://sourceforge.net/projects/boost/files/boost/1.59.0/boost_1_59_0.tar.gz
    wget  --ftp-user=ftpuser --ftp-password=ftpuser.2016   ftp://121.201.104.71/boost_1_59_0.tar.gz
    wget  --ftp-user=ftpuser --ftp-password=ftpuser.2016   ftp://121.201.104.71/sandai_caizuxing/my.cnf
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
    /usr/local/cmake/bin/cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql-5.7.9 -DMYSQL_DATADIR=/data/mysql-5.7.9/data -DWITH_INNOBASE_STORAGE_ENGINE=1  -DWITH_EMBEDDED_SERVER=OFF  -DWITH_BOOST=/usr/local/boost
    make && make install
    chown mysql:mysql -R /usr/local/mysql-5.7.9
	filenum=`ls /usr/local/mysql |wc -l`
	if [ $? -eq 0 ];then
		rm -rf /usr/local/mysql
	    ln -s /usr/local/mysql-5.7.9 /usr/local/mysql
	fi
#	mv /etc/my.cnf /etc/my.cnf.bak
#    cp /usr/local/mysql-5.7.9/support-files/my-default.cnf /etc/my.cnf
    cp /usr/local/mysql-5.7.9/support-files/mysql.server /etc/init.d/mysqld 
    chmod a+x /etc/init.d/mysqld
    chkconfig mysqld on
    
#    #############################my.cnf###############################
#    sed -i "s/# datadir =.*/datadir = \/usr\/local\/mysql-5.7.9\/data /g" /etc/my.cnf
#    sed -i "s/^# basedir =.*/basedir = \/usr\/local\/mysql-5.7.9 /g" /etc/my.cnf
#    sed -i "s/^# port =.*/port = 3306 /g" /etc/my.cnf
    
    #/usr/local/mysql-5.7.9/bin/mysqld --initialize --user=mysql --basedir=/usr/local/mysql-5.7.9/ --datadir=/usr/local/mysql-5.7.9/data/
    #######修改mysql配置文件############
    mkdir /usr/local/mysql-5.7.9/run  /data/mysql-5.7.9/logs /data/mysql-5.7.9/data
    chown mysql:mysql /usr/local/mysql-5.7.9/run /data/mysql-5.7.9/ -R
	mv -f $src_wget_dir/my.cnf /etc/my.cnf
	yum install mysql-devel -y
    #alter user 'root'@'localhost' identified by 'sd-9898w';
}
		 
Install_nginx()
{
    cd $src_wget_dir
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
    #################config iptablesl####################
    sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
    sed -i "/COMMIT/d" /etc/sysconfig/iptables
    echo '-A RH-Firewall-1-INPUT -m state --state NEW -m tcp -p tcp --dport 80 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
    service iptables restart
}

Install_php()
{
    cd $src_wget_dir
    version=5.6.14
    yum install -y freetype-devel libpng-devel libjpeg-devel libxml2-devel bzip2 bzip2-devel libcurl libcurl-devel readline-devel
    wget  ftp://mcrypt.hellug.gr/pub/crypto/mcrypt/libmcrypt/libmcrypt-2.5.7.tar.gz
    tar xzvf libmcrypt-2.5.7.tar.gz
    cd libmcrypt-2.5.7
    ./configure --prefix=/usr/local/libmcrypt/
    make && make install
    
    cd $src_wget_dir
    wget http://ncu.dl.sourceforge.net/project/mhash/mhash/0.9.9.9/mhash-0.9.9.9.tar.bz2
    tar xjvf mhash-0.9.9.9.tar.bz2
    cd mhash-0.9.9.9
    echo "/usr/local/lib/" >>/etc/ld.so.conf
    ldconfig -v
    ./configure --prefix=/usr/local/mhash
    make && make install
    
    cd $src_wget_dir
    wget http://ncu.dl.sourceforge.net/project/mcrypt/MCrypt/2.6.8/mcrypt-2.6.8.tar.gz
    tar xvf mcrypt-2.6.8.tar.gz 
    cd mcrypt-2.6.8
    export LD_LIBRARY_PATH=/usr/local/libmcrypt/lib:/usr/local/mhash/lib
    export LDFLAGS="-L/usr/local/mhash/lib -I/usr/local/mhash/include/"
    export CFLAGS="-I/usr/local/mhash/include/"
    ./configure --prefix=/usr/local/mcrypt --with-libmcrypt-prefix=/usr/local/libmcrypt
    make && make install
    
    ###############要再安装一次，要不然会报错：找不到mcrypt.h文件#############
    cd $src_wget_dir
    cd libmcrypt-2.5.7
    ./configure 
    make && make install
    ###############因为在上面的步骤中已经echo "/usr/local/lib/" >>/etc/ld.so.conf，ldconfig -v解决--enable-opcache=no这个错误#####################
    ldconfig -v 
    
    cd $src_wget_dir
    #5.3.8用这个下载
    #wget http://museum.php.net/php5/php-${version}.tar.gz
    wget http://cn2.php.net/distributions/php-5.6.14.tar.gz
    tar zxvf php-${version}.tar.gz
    cd php-${version}
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
}
check_php_configure()
{
	result=0
	if [ -f /usr/local/php/etc/php.ini ];then
		return $result
    else
        echo -e "\033[31m PHP configuration file is not /usr/local/php/etc/php.ini,please reconfigure php redis!!\033[0m"
		result=1
		return $result
    fi
}

Install_php_redis()
{
	if [ ! -f /usr/local/php/bin/phpize ];then
		echo -e "\033[31m Not install PHP program!! \033[0m"
	fi	
    cd $src_wget_dir
    yum install git -y
    git clone https://github.com/phpredis/phpredis.git
    cd phpredis/
    /usr/local/php/bin/phpize
    ./configure --with-php-config=/usr/local/php/bin/php-config
    make && make install
	check_php_configure
	result=$?
	if [  $result -eq 0 ];then
#	if [ -f /usr/local/php/etc/php.ini ];then 
		sed -i '/user_dir/a extension_dir = "/usr/local/php-5.6.14/lib/php/extensions/no-debug-non-zts-20131226/" \nextension = redis.so' /usr/local/php/etc/php.ini
		killall php-fpm
		/usr/local/php/sbin/php-fpm
#	else
#		echo -e "\033[31m PHP configuration file is not /usr/local/php/etc/php.ini,please reconfigure php redis!!\033[0m"
	fi
}

Install_php_mongo()
{
	if [ ! -f /usr/local/php/bin/phpize ];then
		echo -e "\033[31m Not install PHP program!! \033[0m"
	fi	
    cd $src_wget_dir
    wget http://pecl.php.net/get/mongo-1.4.0.tgz 
    tar xvf mongo-1.4.0.tgz
    cd mongo-1.4.0
    /usr/local/php/bin/phpize 
    ./configure --with-php-config=/usr/local/php/bin/php-config 
    make && make install
	check_php_configure
	result=$?
	if [  $result -eq 0 ];then
		grep "no-debug-non-zts-20131226" /usr/local/php/etc/php.ini
		if [ $? -eq 0 ];then
			sed -i '/no-debug-non-zts-20131226/a extension = mongo.so'  /usr/local/php/etc/php.ini
			killall php-fpm
			/usr/local/php/sbin/php-fpm
		fi
	fi
}

Install_php_memcache()
{
	if [ ! -f /usr/local/php/bin/phpize ];then
		echo -e "\033[31m Not install PHP program!! \033[0m"
	fi	
    cd $src_wget_dir
    wget http://pecl.php.net/get/memcache-2.2.7.tgz
    tar zxvf memcache-2.2.7.tgz
    cd memcache-2.2.7
    /usr/local/php/bin/phpize
    ./configure --with-php-config=/usr/local/php/bin/php-config
    make && make install
	check_php_configure
	result=$?
	if [  $result -eq 0 ];then
        grep "no-debug-non-zts-20131226" /usr/local/php/etc/php.ini
        if [ $? -eq 0 ];then
            sed -i '/no-debug-non-zts-20131226/a extension = memcache.so'  /usr/local/php/etc/php.ini
			killall php-fpm
			/usr/local/php/sbin/php-fpm
        fi
    fi
}

Install_rsync()
{
    if [ -e /etc/rsyncd.conf ]
    then
    echo exist
    exit
    fi
    if [ ! -d /etc/rsyncd/ ]; then
    	mkdir /etc/rsyncd/
    fi
    mkdir -p  /var/log/rsync
    touch   /var/log/rsync.lock
    cat >/etc/rsyncd.conf <<CONF
    uid = nobody
    gid = nobody
    port = 3873
    use chroot = no
    max connections = 4000
    timeout=600
    #syslog facility = local5
    pid file = /var/log/rsync/rsyncd.pid
    log file = /var/log/rsync/rsync.log
    lock file= /var/log/rsync/rsync.lock
    
    [online]
            path = /
            refuse options =  delete
            comment = tel-cdn
            read only= no
           # ignore errors
            auth users = kankan
            secrets file = /etc/rsyncd/rsyncd.secrets
CONF
    yum install -y rsync xinetd
    echo "kankan:D2%k)sZ(6N" >/etc/rsyncd/rsyncd.secrets
    chmod 600 /etc/rsyncd/rsyncd.secrets
    
    sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
    sed -i "/COMMIT/d" /etc/sysconfig/iptables
    echo '-A RH-Firewall-1-INPUT -m state  --state NEW -m tcp -p tcp --dport 3873 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
    echo "/usr/bin/rsync --daemon" >>/etc/rc.local
    
    /usr/bin/rsync --daemon
    service iptables restart
}

Install_ftp()
{
    cd $src_wget_dir
    wget http://thunder:xunlei@apt.sandai.net/tmp/pureftp/pure-ftpd-1.0.36.tar.gz
    tar -xvf pure-ftpd-1.0.36.tar.gz
    cd pure-ftpd-1.0.36
    ./configure --prefix=/usr/local/pure-ftpd-1.0.36 --with-everything
    make
    make install
    mkdir -p /usr/local/pure-ftpd-1.0.36/etc/
    cp ./configuration-file/pure-ftpd.conf /usr/local/pure-ftpd-1.0.36/etc/
    cp ./configuration-file/pure-config.pl /usr/local/pure-ftpd-1.0.36/bin/
    chmod a+x /usr/local/pure-ftpd-1.0.36/bin/pure-config.pl
    
    cd /usr/local/
    ln -s pure-ftpd-1.0.36 pure-ftpd
    ln -s /usr/local/pure-ftpd-1.0.36/bin/pure-pw /usr/local/bin/pure-pw
    
    echo "/usr/local/pure-ftpd-1.0.36/bin/pure-config.pl /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf" >> /etc/rc.d/rc.local
    sed -i 's#MinUID                      100#MinUID                      98#g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/MaxClientsNumber            50/MaxClientsNumber            1000/g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/# TrustedGID                    100/TrustedGID                    99/g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/MaxClientsPerIP             8/MaxClientsPerIP             200/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/AllowUserFXP                no/AllowUserFXP                yes/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/MaxDiskUsage               99/MaxDiskUsage               97/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -i 's/# PassivePortRange          30000 50000/PassivePortRange          30000 50000/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    sed -ir '/\/etc\/pureftpd.pdb/a\PureDB                        /usr/local/pure-ftpd/etc/pureftpd.pdb' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    
    /usr/local/pure-ftpd-1.0.36/bin/pure-config.pl /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
    ###################
    sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
    sed -i "/COMMIT/d" /etc/sysconfig/iptables
    echo '#office
-A RH-Firewall-1-INPUT -s 116.7.233.118 -m multiport -p tcp --dport 21,30000:50000 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
    service iptables restart
    ##################
    chattr -i /etc/hosts.allow
    echo -e '#kankan office \npure-ftpd:116.7.233.118' >> /etc/hosts.allow
    touch /usr/local/pure-ftpd-1.0.36/etc/pureftpd.passwd
    chattr +i /etc/hosts.allow
}

Install_node()
{
    cd $src_wget_dir
    wget https://nodejs.org/dist/v4.3.1/node-v4.3.1-linux-x64.tar.xz
    tar xvf node-v4.3.1-linux-x64.tar.xz -C /usr/local
    cd /usr/local
    mv node-v4.3.1-linux-x64 node-v4.3.1
    ln -s /usr/local/node-v4.3.1/bin/npm /usr/bin/npm
    ln -s /usr/local/node-v4.3.1/bin/node /usr/bin/node
}

Install_redis_6379()
{
    cd $src_wget_dir
    wget  http://download.redis.io/releases/redis-3.0.5.tar.gz
    tar xvf redis-3.0.5.tar.gz
    mv redis-3.0.5 /usr/local/
    cd /usr/local/redis-3.0.5/
    make
    echo "vm.overcommit_memory = 1" >>/etc/sysctl.conf
    sysctl vm.overcommit_memory=1
    mkdir -pv /data/redis/logs
    ports="6379"
    for port in `echo $ports`
    do
            mkdir -pv /data/redis/data/$port
            cp /usr/local/redis-3.0.5/redis.conf /etc/redis-$port\.conf
            sed -i "s/daemonize no/daemonize yes/" /etc/redis-$port\.conf
            sed -i "/logfile/d" /etc/redis-$port\.conf
            sed -i "/daemonize yes/a logfile \/data\/redis\/logs\/redis-$port\.log" /etc/redis-$port\.conf
            sed -i 's/port 6379/port '$port'/' /etc/redis-$port\.conf
            sed -i "s/# syslog-enabled no/syslog-enabled no/" /etc/redis-$port\.conf
            sed -i "s/dir .\//dir \/data\/redis\/data/$port" /etc/redis-$port\.conf
            /usr/local/redis-3.0.5/src/redis-server /etc/redis-$port\.conf &
            echo -e "\n"  >> /etc/rc.local
            echo "/usr/local/redis-3.0.5/src/redis-server /etc/redis-$port\.conf &" >>/etc/rc.local
    done
}

Install_redis_6379_and_6380()
{
    cd $src_wget_dir
    wget  http://download.redis.io/releases/redis-3.0.5.tar.gz
    tar xvf redis-3.0.5.tar.gz
    mv redis-3.0.5 /usr/local/
    cd /usr/local/redis-3.0.5/
    make
    echo "vm.overcommit_memory = 1" >>/etc/sysctl.conf
    sysctl vm.overcommit_memory=1
    mkdir -pv /data/redis/logs
    ports="6379 6380"
    for port in `echo $ports`
    do
            mkdir -pv /data/redis/data/$port
            cp /usr/local/redis-3.0.5/redis.conf /etc/redis-$port\.conf
            sed -i "s/daemonize no/daemonize yes/" /etc/redis-$port\.conf
            sed -i "/logfile/d" /etc/redis-$port\.conf
            sed -i "/daemonize yes/a logfile \/data\/redis\/logs\/redis-$port\.log" /etc/redis-$port\.conf
            sed -i 's/port 6379/port '$port'/' /etc/redis-$port\.conf
            sed -i "s/# syslog-enabled no/syslog-enabled no/" /etc/redis-$port\.conf
            sed -i "s/dir .\//dir \/data\/redis\/data/$port" /etc/redis-$port\.conf
            /usr/local/redis-3.0.5/src/redis-server /etc/redis-$port\.conf &
            echo -e "\n"  >> /etc/rc.local
            echo "/usr/local/redis-3.0.5/src/redis-server /etc/redis-$port\.conf &" >>/etc/rc.local
    done
}

Install_memcache()
{
    cd $src_wget_dir
    wget http://www.monkey.org/~provos/libevent-2.0.12-stable.tar.gz
    tar zxf libevent-2.0.12-stable.tar.gz
    cd libevent-2.0.12-stable
    ./configure  --prefix=/usr/local/lib
     make && make install
	version=1.4.24
    cd $src_wget_dir
    wget http://memcached.org/files/memcached-${version}.tar.gz
    tar xvf memcached-${version}.tar.gz
    cd memcached-${version}
    ./configure --prefix=/usr/local/memcached-${version} --with-libevent=/usr/local/lib && make && make install
	ln -s /usr/local/memcached-${version} /usr/local/memcached
    /usr/local/memcached/bin/memcached -d -m 4096 -u nobody -p 11211 -c 10240    # c 表示最大并发数
    
    echo "/usr/local/memcached/bin/memcached -d -m 4096 -u nobody -p 11211 -c 10240"  >> /etc/rc.local
}

Install_python()
{
	if [[ "$old_version" =~ "2.7.6" ]];then
		echo -e "\033[31m Python 2.7.6 already install!!"
		exit
	fi	
    cd $src_wget_dir
	old_version=`python -V 2>&1 |awk '{print $2}'`
    wget https://www.python.org/ftp/python/2.7.6/Python-2.7.6.tgz
    tar xzvf Python-2.7.6.tgz
    cd Python-2.7.6
    ./configure --prefix=/usr/local/Python-2.7.6
    make && make install
    sed -i '/export PATH USER LOGNAME MAIL HOSTNAME HISTSIZE HISTCONTROL/ i\PythonPATH=\/usr\/local\/Python-2.7.6\/bin \nPATH=\$PythonPATH:\$PATH' /etc/profile
    source /etc/profile
}
 
Install_setuptools_pip()
{   
	Install_python
    cd $src_wget_dir
    wget https://pypi.python.org/packages/source/s/setuptools/setuptools-20.3.1.tar.gz#md5=7e4ba5cdebc02710d3ab748c103fc673 --no-check-certificate
    tar xvf setuptools-20.3.1.tar.gz
    cd setuptools-20.3.1
    python setup.py install
    
    cd $src_wget_dir
    wget --no-check-certificate https://github.com/pypa/pip/archive/1.5.5.tar.gz
    tar xvf 1.5.5.tar.gz
    cd pip-1.5.5
    python setup.py install
}

Install_mongodb（）
{
	version=3.0.6
	wget http://fastdl.mongodb.org/linux/mongodb-linux-x86_64-${version}.tgz
	tar zxvf mongodb-linux-x86_64-${version}.tgz
	mv mongodb-linux-x86_64-3.0.6 /usr/local/
	ln -s mv /usr/local/mongodb-linux-x86_64-3.0.6  /usr/local/mongodb
	echo "
port=27017 #端口号
fork=true #以守护进程的方式运行，创建服务器进程
logpath=/usr/local/mongodb/mongodb.log #日志输出文件路径
logappend=true #日志输出方式
dbpath=/data/db #数据库路径
shardsvr=true #设置是否分片
maxConns=10240 #数据库的最大连接数
slave=true
#source=42.51.169.104:27017   #指定主mongodb server
source=182.118.125.68:27017   #指定主mongodb server
slavedelay=10               #延迟复制，单位为秒
autoresync=true   " >> /usr/local/mongodb/mongodb.conf
	/usr/local/mongodb/bin/mongod -f /usr/local/mongodb/mongodb.conf
	echo "/usr/local/mongodb/bin/mongod -f /usr/local/mongodb/mongodb.conf" >>/etc/rc.local
}

print_menu()
{
	echo -e "##############################################"
	echo -e "# \033[31m1).You will install mysql-5.7.9.\033[0m           #"
	echo -e "# \033[31m2).You will install nginx-1.8.0.\033[0m           #"
	echo -e "# \033[31m3).You will install php-5.6.14.\033[0m            #"
	echo -e "# \033[31m4).You will install php-redis-extension.\033[0m   #"
	echo -e "# \033[31m5).You will install php-mongodb-extension.\033[0m #"
	echo -e "# \033[31m6).You will install php-memcache-extension.\033[0m#"
	echo -e "# \033[31m7).You will install python-2.7.6.\033[0m          #"
	echo -e "# \033[31m8).You will install port 6379 redis-3.0.5 .\033[0m#"
	echo -e "# \033[31m9).You will install port 6379 6380 redis.\033[0m  #"
	echo -e "# \033[31m10).You will install node-4.3.1.\033[0m           #"
	echo -e "# \033[31m11).You will install memcache-1.4.20.\033[0m      #"
	echo -e "# \033[31m12).You will install rsync.\033[0m                #"
	echo -e "# \033[31m13).You will install ftp.\033[0m                  #"
	echo -e "# \033[31m14).You will install setuptool-pip.\033[0m        #"
	echo -e "# \033[31m15).You will install mongodb.\033[0m              #"
	echo -e "# \033[31mq).quit.\033[0m                                   #"
	echo -e "##############################################"
	read -p 'please input a number to install software or input "q" to quit:' number
	if [[ $number =~ [a-z|A-Z] ]];then
		exit
	fi 
	return $number
}

main()
{
	src_wget_dir="/usr/local/src"
	yum install wget gcc gcc-c++ ncurses* openssl openssl-devel -y
	if [ ! -d $src_wget_dir/ ];then
	        mkdir $src_wget_dir
	fi
	case $menu_result in
		"1")
			Install_mysql
			if [ $? -eq 0 ];then
				echo -e "\033[31mMysql-5.7.9 install is success!\033[0m"
				echo "You need to complete the mysql initalization and change mysql password."
				sleep 5
			else
				echo -e "Install mysql-5.7.9 is wrong!"
			fi
			;;
		"2")
			Install_nginx
			nginx_num=`ps aux | grep nginx | grep -v grep |wc -l` 
			if [ $nginx_num -gt 1 ];then
				echo -e "\033[31mNginx-1.8.0 install is success!\033[0m"
				sleep 5
			else
				echo -e "Install nginx-1.8.0 is wrong!"
			fi
			;;
		"3")
			Install_php
			php_num=`ps aux | grep php | grep -v grep |wc -l`
			if [ $php_num -gt 1 ];then
                echo -e "\033[31mPHP-5.6.14 install is success!\033[0m"
                sleep 5
            else
                echo -e "Install php-5.6.14 is wrong!"
            fi
            ;;
		"4")
			Install_php_redis;;
		"5")
			Install_php_mongo;;
		"6")
			Install_php_memcache;;
		"7")
			Install_python;;
		"8")
			Install_redis_6379;;
		"9")
			Install_redis_6379_and_6380;;
		"10")
			Install_node;;
		"11")
			Install_memcache;;
		"12")
			Install_rsync
			ps aux | grep rsync
			if [ $? -eq 0 ];then
				echo -e "\033[31m Rsync install is success!\033[0m"
				read -p "Please input your rsync client's IP(If there are many IP to 10.1.1.10  10.1.1.11 file like this):" server_ip
				chattr -i /etc/hosts.allow
				echo "rsync:$server_ip" >>/etc/hosts.allow
				chattr +i /etc/hosts.allow
				echo -e "Usage:
							1.If the /etc/rsyncd/rsyncd.secrets doesn't exist,you should touch /etc/rsyncd/rsyncd.secrets file and echo D2%k)sZ(6N >/etc/rsyncd/rsyncd.secrets.Then you need chmod 600 /etc/rsyncd/rsyncd.secrets.
							2.After completing the first step,you can rsync files to rsync server.
							3.Cmd is rsync -avzrP --port=3873 favicon.ico  kankan@27.148.182.92::online/data/vhosts/ --password-file=/etc/rsyncd/rsyncd.secrets.(favicon.ico is you want to upload files.27.148.182.92 is rsync server's IP./data/vhosts/ is rsync server's path.)"
			fi
			;;
		"13")
			Install_ftp
			ps aux | grep ftp
			if [ $? -eq 0 ];then
				echo -e "\033[31m FTP installl is success!\033[0m"
				echo  "Please use RTX contact caizuxing create the FTP accounts!" 
#				read -p "Please input the FTP dir:" ftpdir
#				if [ ${#ftpuser} -ne 0 ];then
#					pure-pw useradd $ftpuser -u 99 -g 99 -d $ftpdir
#					pure-pw mkdb
#				else
#					echo -e "FTP accout can't be empty!!!"
#				fi
			else
				echo -e "FTP install is wrong!"
			fi
			;;
		"14")
			Install_setuptool_pip;;
		"15")
			Install_mongodb;;
		"q")
			exit;;
		"*")
			exit;;
esac
}

read -p "Whether or not to continue:[yes|no]" key
case $key in
	"yes")
		while true
		do
			print_menu
			menu_result=$?
			main
		done
		;;
	"no")
		exit
		;;
	"*")
		exit 
		;;
esac
