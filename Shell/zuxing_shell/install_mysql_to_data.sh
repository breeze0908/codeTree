#!/bin/bash
yum install wget gcc gcc-c++ ncurses* openssl openssl-devel -y

if [ ! -d /usr/local/src/ ];then
        mkdir /usr/local/src
fi

########安装mysql###########
cd /usr/local/src
wget  http://www.cmake.org/files/v2.8/cmake-2.8.3.tar.gz
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
/usr/local/cmake/bin/cmake -DCMAKE_INSTALL_PREFIX=/usr/local/mysql-5.7.9 -DMYSQL_DATADIR=/data/mysql-5.7.9/data -DWITH_INNOBASE_STORAGE_ENGINE=1  -DWITH_EMBEDDED_SERVER=OFF  -DWITH_BOOST=/usr/local/boost
make && make install
chown mysql:mysql -R /usr/local/mysql-5.7.9
ln -s /usr/local/mysql-5.7.9 /usr/local/mysql
mv /etc/my.cnf /etc/my.cnf.bak
cp /usr/local/mysql-5.7.9/support-files/my-default.cnf /etc/my.cnf
cp /usr/local/mysql-5.7.9/support-files/mysql.server /etc/init.d/mysqld 
chmod a+x /etc/init.d/mysqld
chkconfig mysqld on

#############################my.cnf###############################
#sed -i "s/# datadir =.*/datadir = \/data\/mysql-5.7.9\/data /g" /etc/my.cnf
#sed -i "s/^# basedir =.*/basedir = \/usr\/local\/mysql-5.7.9 /g" /etc/my.cnf
#sed -i "s/^# port =.*/port = 3306 /g" /etc/my.cnf

#/usr/local/mysql-5.7.9/bin/mysqld --initialize --user=mysql --basedir=/usr/local/mysql-5.7.9/ --datadir=/usr/local/mysql-5.7.9/data/
#######修改mysql配置文件############
mkdir -pv /usr/local/mysql-5.7.9/run  /data/mysql-5.7.9/logs /data/mysql-5.7.9/data
chown mysql:mysql -R /usr/local/mysql-5.7.9/run /data/mysql-5.7.9/

#alter user 'root'@'localhost' identified by 'sd-9898w';
