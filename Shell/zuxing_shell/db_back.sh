#!/bin/bash
curdate=`date +%Y-%m-%d_%H-%M`
backupdir="/opt/backup/cdfdc_db"
databasename="dbname"
username="username"
password="-ppassword"  # if password isn't empty use "-pxxxxx"
#the number of days to keep backups
keepbackups=`date -d '30 days ago' +%Y-%m-%d`

echo =====================
echo Start backup MySQL DB
echo Start Fdc
mysqldump -u${username} ${password} ${databasename} > ${backupdir}/${databasename}_${curdate}.sql
echo Done.

echo Deleting old backups
cd $backupdir
find . -name "${databasename}_${keepbackups}*" | xargs rm -rf
echo Done.
echo =====================




cat /data/mysql/dbbackup/rsync_v_mp4_submovie_KTw01020.sh
#!/bin/bash

curdate=`date '+%Y-%m-%d_%H:%M:%S'`
keepbackups=`date -d '7 days ago' +%Y-%m-%d`
/usr/local/mysql/bin/mysqldump -uroot -psd-9898w --opt --add-drop-table  movie v_mp4_submovie  > /data/mysql/dbbackup/v_mp4_submovie_$curdate.sql

cd  /data/mysql/dbbackup/
tar czvf /data/mysql/dbbackup/v_mp4_submovie_$curdate.sql.tar.gz v_mp4_submovie_$curdate.sql

mv v_mp4_submovie_$curdate.sql.tar.gz v_mp4_submovie.sql.tar.gz

echo -e "================================ \nStart rsync movie.v_mp4_submovie.sql.tar.gz to KTw01020 on $curdate" >>rsync_KTw01020.log
rsync -avzP /data/mysql/dbbackup/v_mp4_submovie.sql.tar.gz mysql_back@121.201.104.85::backup --password-file=/etc/rsyncd.secrets-KTw01020
if [ $? -eq 0 ];then
	cd /data/mysql/dbbackup/
	rm -f v_mp4_submovie.sql.tar.gz
fi
echo "Done" >> rsync_KTw01020.log

echo "Deleting old backups" >>rsync_KTw01020.log
cd /data/mysql/dbbackup/
find ./ -name "v_mp4_submovie_${keepbackups}*" | xargs rm -rf
echo -e "Done.\n================================\n" >>rsync_KTw01020.log


cat /data2/mysql_backup_twin14467/mysql_movie_v_mp4_submovie.sh
#!/bin/bash

backupdir="/data2/mysql_backup_twin14467"
curdate=`date '+%Y-%m-%d_%H:%M:%S'`
#keepbackups=`date -d '7 days ago' +%Y-%m-%d`



cd $backupdir


if [ -f $backupdir/v_mp4_submovie.sql.tar.gz ];then
        tar xvf v_mp4_submovie.sql.tar.gz
        if [ -f $backupdir/v_mp4_submovie.sql ];then
                echo  -e "================================ \nStart update  movie.v_mp4_submovie.sql $curdate" >>mysql_dump.log
                /usr/local/mysql/bin/mysql -uroot -psd-9898w movie < $backupdir/v_mp4_submovie.sql
                echo "Done" >> mysql_dump.log   
        fi
else
        echo "No movie.v_mp4_submovie.sql file uploaded to the server" >>mysql_dump.log
fi      


echo "Deleting old backups" >> mysql_dump.log
cd $backupdir
#find ./ -name "v_mp4_submovie_${keepbackups}*" | xargs rm -rf
rm -f v_mp4_submovie.sql.tar.gz v_mp4_submovie.sql
echo -e "Done.\n================================\n" >> mysql_dump.log
