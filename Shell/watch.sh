#!/bin/bash
#
# crontab命令：
# 监控线上服务稳定性情况
# */1 * * * * sh /home/work/script/monitor/Watch.sh start >> /home/work/logs/script_monitor_watch.log

dir=$(cd "$(dirname "$0")"; cd ..; pwd)
cd $dir

# PHP命令
php="php artisan"

# 在这里配置所有需要【守护】的PHP进程
proc_list=('message:add' 'message:send')

#work 账户运行
name=$(whoami)
if [ $name != 'work' ];then
    echo `date "+%Y/%m/%d %H:%M:%S> "` "必须用work账户"
    #exit
fi

#开启服务
start() {
    for proc in ${proc_list[@]} ;do
        arrm=$(ps -ef | grep "`echo $proc`" | grep -v 'grep' | awk '{print $2}' | wc -l)
        if [ ${arrm:-0} = 0 ];then
           $php $proc >/dev/null &
           echo `date "+%Y/%m/%d %H:%M:%S> "` "$proc 进程已经重启"
        else
           echo `date "+%Y/%m/%d %H:%M:%S> "` "$proc 进程已经存在"
        fi
    done
}

#停止服务
stop() {
    for proc in ${proc_list[@]} ;do
        arrproc=$(ps -ef | grep "`echo $proc`" | awk '{print $2}')
        for p in $arrproc; do
            kill $p;
            echo `date "+%Y/%m/%d %H:%M:%S> "` $p " 进程已杀死！"
        done
    done
    echo `date "+%Y/%m/%d %H:%M:%S> "` "服务已停止！"
}

#check脚本是否运行
check() {
    for proc in ${proc_list[@]} ;do
        arrspar=$(ps -ef | grep "`echo $proc`" | grep -v 'grep' | awk '{print $2}')
        echo `date "+%Y/%m/%d %H:%M:%S> "` "目前运行的服务监控进程($proc)：" ${arrspar:-"无"}
    done
}

usage() {
    cat <<EOF
        守护进程使用方法（需要 work 用户执行）:

        usage: sh $0 check|start|stop|restart
        start       启动服务
        stop        停止服务
        check       检查服务是否正常
EOF
        exit
}

while true;do
    case $1 in
        start)
            start
            break
            ;;
        help)
            usage
            break
            ;;
        stop)
            stop
            break
            ;;
        check)
            check
            break
            ;;
        *)
            usage
            break
            ;;
    esac
    shift
done
