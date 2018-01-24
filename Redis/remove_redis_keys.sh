#! /usr/bin/env bash


REDIS_CLI=/usr/local/redis/bin/redis-cli

if [ ${#} -eq 0 ];then
   echo "missing params, please tell me which keys would be remove!"
   exit;
fi

HOST='127.0.0.1'
PORT=6379
keyPrefix=''


if [ ${#} -gt 2 ];then
   HOST=$1
   PORT=$2
   keyPrefix=$3
elif [ ${#} -gt 1 ];then
   HOST=$1
   keyPrefix=$2
else
   keyPrefix=$1
fi

echo "connecting to ${HOST}....."
sleep 5

CLIENT="${REDIS_CLI} -h ${HOST} -p ${PORT}" 

#列出所有的keys
${CLIENT} keys ${keyPrefix}
#开始删除
${CLIENT} keys ${keyPrefix} | xargs ${CLIENT} del
echo "redis_key:[${keyPrefix}] have been removed....."
