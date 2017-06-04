#!/bin/bash
HOSTNAME=`hostname`
curdate=`date '+%Y-%m-%d_%H:%M:%S'`
Databases="star_v4_comment broadcast"
for Database in `echo $Databases`
do
        /usr/local/mysql/bin/mysqldump -h 127.0.0.1 -P 3307 -uroot -psd-9898w --opt --add-drop-table --databases $Database  > /data/dbbackup/${Database}_${curdate}.sql
        cd  /data/dbbackup/
        tar czvf /data/dbbackup/${Database}_${curdate}.sql.tar.gz ${Database}_${curdate}.sql
done

for Database in `echo $Databases`
do
        echo -e "================================ \nStart rsync $Databse_$curdate.sql.tar.gz to KTr01205 on $curdate" >>/data/dbbackup/rsync_KTr01205.log
        rsync -avzP --port=3873  "/data/dbbackup/${Database}_${curdate}.sql.tar.gz" kankan@10.1.1.205::mysqlbackup/3307 --password-file=/etc/rsyncd.secrets-KTr01205
        if [ $? -eq 0 ];then
                echo "Done" >> /data/dbbackup/rsync_KTr01205.log
        else
                echo -e ${Database}_${curdate}.sql.tar.gz "rsync is failed!" >> /data/dbbackup/rsync_KTr01205.log
                failed_info="Mysql rsync is failed.Please notice it!"
                /usr/local/monitor-base/bin/sendEmail -s mail.cc.kankan.com -f monitor@cc.kankan.com  -t caizuxing@kankan.com -xu monitor@cc.kankan.com -xp UGtx3MMH  -u "$failed_info"  -m "$HOSTNAME  mysql 3307 backup file rsync is failed!!"
        fi
        sleep 3
done

echo "Deleting old backups" >>/data/dbbackup/rsync_KTr01205.log
cd /data/dbbackup/
find ./ -mtime +7 -name "*.sql" | xargs rm -rf
find ./ -mtime +7 -name "*.tar.gz" | xargs rm -rf
echo -e "Done.\n================================\n" >>/data/dbbackup/rsync_KTr01205.log
