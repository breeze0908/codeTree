#!/usr/bin/env bash

PORT=$1
CONF=/usr/local/redis/etc/redis_${PORT}.conf

if [ ${#} -eq 0 ];then
   echo "missing params, please tell me which port would be add!"
   exit;
fi


cd /usr/local/redis/
cp /usr/local/redis/etc/redis.conf ${CONF}

#端口设置
sed -i "s/daemonize no/daemonize yes/" ${CONF}
sed -i "/logfile/d" ${CONF}
sed -i "s/port [0-9]\+$/port ${PORT}/" ${CONF}
sed -i "s/pidfile \/var\/run\/redis_6379.pid/pidfile \/usr\/local\/redis\/redis_${PORT}.pid/" ${CONF}
sed -i "/daemonize yes/a logfile \/usr\/local\/redis\/var\/redis_${PORT}.log" ${CONF}


# 禁用持久化
sed -i "s/#   save ""/save \"\"/" ${CONF}
sed -i "s/save 900 1/# save 900 1/" ${CONF}
sed -i "s/save 300 10/# save 300 10/" ${CONF}
sed -i "s/save 60 10000/# save 60 10000/" ${CONF}



#禁用函数
#sed -i "/# rename-command CONFIG \"\"/a rename-command FLUSHALL \"\"" ${CONF}
#sed -i "/# rename-command CONFIG \"\"/a rename-command FLUSHDB \"\"" ${CONF}
#sed -i "/# rename-command CONFIG \"\"/a rename-command CONFIG \"\"" ${CONF}
#sed -i "/# rename-command CONFIG \"\"/a rename-command KEYS \"\"" ${CONF}


#最大内存 - $((4*1024*1024*1024)) == 4G
MAX_MEM=$((4*1024*1024*1024));
#sed -i "s/maxmemory <[0-9]\+>/maxmemory ${MAX_MEM}/" ${CONF}
sed -i "/# maxmemory <bytes>/a maxmemory ${MAX_MEM}" ${CONF}


#服务
/usr/local/redis/src/redis-server ${CONF} &
echo -e "\n"  >> /etc/rc.local
echo "/usr/local/redis/src/redis-server ${CONF} &" >>/etc/rc.local