###  Redis 实例 （不需要集群）
* 版本：3.2.11
* 机器：63.159.217.XX(redis 01)、 63.159.217.XX(redis 02)
* 其他：禁用flushall, flushdb, config, keys四个命令

| redis实例名称       | 端口  | 配置    | 机器     | 最大内存 | 主从 |
|--------------------|:----:|--------|----------|:--------:|:----:|
| message_push_redis | 6380 | 持久化  | redis 01 |    4G    |  否  |
| session            | 6379 |        | redis 02 |    4G    |  否  |
| filmora_redis      | 6381 |        | redis 02 |    3G    |  否  |
| store_redis        | 6382 |        | redis 02 |    3G    |  否  |
| user_redis         | 6383 |        | redis 02 |    3G    |  否  |



### 主要配置修改

####  配置文件路径： 
```sh
/usr/local/redis/etc/redis_${PORT}.conf
```

####  配置修改内容：
```sh
daemonize yes
port ${PORT}
pidfile /usr/local/redis/redis_${PORT}.pid/
logfile /usr/local/redis/var/redis_${PORT}.log


# 禁用持久化
save ""
# save 900 1
# save 300 10
# save 60 10000

# 禁用函数 FLUSHALL、FLUSHDB、CONFIG
rename-command FLUSHALL ""
rename-command FLUSHDB ""
rename-command CONFIG ""
rename-command SHUTDOWN ""
rename-command KEYS ""

#最大内存 - $((4*1024*1024*1024)) == 4G
maxmemory 4294967296
```

### 开机启动

```sh
/usr/local/redis/src/redis-server /usr/local/redis/etc/redis_${PORT}.conf &
echo -e "\n"  >> /etc/rc.local
echo "/usr/local/redis/src/redis-server /usr/local/redis/etc/redis_${PORT}.conf &" >>/etc/rc.local
```