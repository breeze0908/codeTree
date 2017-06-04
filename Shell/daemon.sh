#!/bin/bash

#host=`hostname`.xxx.net
php_dir="/usr/local/bin"
project_dir="/var/www/app.xxx.com/v1"


check_fix_focus_total() {
                line=`ps aux |grep -v grep |grep -w fix_focus_total |wc -l`
                if [ $line -eq 0 ];then
                        DATE=`date '+%Y-%m-%d_%H:%M:%S'`
#                       err_info="$host $project_dir/artisan user:fix_focus is down,The time is $DATE"
#                       /usr/local/monitor-base/bin/sendEmail -s mail.xxx.com -f monitor@cc.xxx.com  -t xxx@cc.xxx.com -xu monitor@cc.xxx.com -xp XWIU3MMH  -u "$err_info"  -m " "
                        $php_dir/php -c /usr/local/php/etc/php.ini $project_dir/artisan user:fix_focus_total >>/tmp/artisan_fix_focus_total.log 2>&1 &
                        start_date=`date '+%Y-%m-%d_%H:%M:%S'`
                        echo -e "#######artisan user:fix_focus on $start_date is start##### \n" >>/tmp/artisan_fix_focus_total.log
                        success_info="$host $project_dir/artisan user:fix_focus is starting.The time is $start_date"
#                        /usr/local/monitor-base/bin/sendEmail -s mail.cc.kankan.com -f monitor@cc.kankan.com  -t caizuxing@cc.kankan.com -xu monitor@cc.kankan.com -xp UGtx3MMH  -u "$success_info"  -m " "
                fi
}




check_mongod() {
                line=`ps aux |grep -v grep |grep -w mongod |wc -l`
                if [ $line -eq 0 ];then
                        DATE=`date '+%Y-%m-%d_%H:%M:%S'`
			/usr/local/mongodb-3.2.7/bin/mongod --dbpath=/usr/local/mongodb-3.2.7/mongodb_db/ --logpath=/usr/local/mongodb-3.2.7/mongodb_logs/mongodb.log --logappend&
                        start_date=`date '+%Y-%m-%d_%H:%M:%S'`
                        echo -e "######## start mongod servive ########\n" >>/usr/local/mongodb-3.2.7/mongodb_logs/mongodb.log
                fi
}

check_robot_pop() {

        #robot_pop子进程数
        PROCESS_NUM=5
        total=`ps aux |grep -v grep |grep -w robot:pop |wc -l`
        #echo "从ps读取的total为：" $total

        if [ $total -lt $PROCESS_NUM ]; then
                num=$[ $PROCESS_NUM - $total ]
                start_date=`date '+%Y-%m-%d_%H:%M:%S'`
                #echo -e "##### robot:pop on $start_date ps count is $total ##### \n" >>/tmp/robot_logs/robot_pop.log

                for (( i = 0; i < $num; i++)) do
                        $php_dir/php -c /usr/local/php/etc/php.ini $project_dir/artisan robot:pop >>/tmp/robot_logs/robot_pop.log 2>&1 &
                        echo -e "##### robot:pop on $start_date is start ##### \n" >>/tmp/robot_logs/robot_pop.log
                done
        fi
}



while true
do
	#check_fix_focus
	#check_fix_focus_total
	#check_live_start_push
	#check_ranking_create_hot
	#check_push_dequeue
	#check_log_dequeue

	check_live_info
	check_mongod
	check_robot_pop

	sleep 5
done