#!/bin/bash
RSYNCDATA=`date +"%Y-%m-%d"`
echo $RSYNCDATA
echo "======start rsync======="
rsync -avzP root@120.41.46.12:/data2/db_remote_backup/twin14467/mysql/movie /data1/mysqldata/
chown -R mysql:mysql /data1/mysqldata/movie
#rsync -avzP root@120.41.46.12:/data2/db_remote_backup/twin14467/mysql/data/video /usr/local/mysql/data/
#chown -R mysql:mysql /usr/local/mysql/data/video
echo "======start  mysqlcheck======="
#/usr/local/mysql/bin/mysqlcheck -r movie -uroot -psd-9898w 
/usr/local/mysql/bin/mysqlcheck  -S /usr/local/mysql-5.7.9/data//mysql.sock.lock  -psd-9898w -r movie
#/usr/local/mysql/bin/mysqlcheck -r video -uroot -psd-9898w 
/etc/init.d/mysqld restart
