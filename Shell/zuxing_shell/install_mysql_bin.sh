#!/bin/bash
tar xf mysql-5.1.72-linux-x86_64-glibc23.tar.gz -C /usr/local/
cd /usr/local/
rmdir mysql
ln -s mysql-5.1.72-linux-x86_64-glibc23 mysql
cd mysql
chown -R mysql:mysql /usr/local/mysql-5.1.72-linux-x86_64-glibc23
cp support-files/my-medium.cnf /etc/my.cnf
scripts/mysql_install_db --user=mysql
cp support-files/mysql.server /etc/init.d/mysqld
chown -R mysql data
./bin/mysqld_safe --user=mysql &
ln -s /usr/local/mysql/bin/mysql /usr/local/bin/
ln -s /usr/local/mysql/bin/mysqladmin /usr/local/bin
rm -rf  /usr/local/mysql/data/*
/etc/init.d/mysqld start
echo "/etc/init.d/mysqld start" >> /etc/rc.local

#/usr/local/mysql/bin/mysqladmin -uroot password "sd-9898w"


cat /etc/my.cnf
# Example MySQL config file for very large systems.
#
# This is for a large system with memory of 1G-2G where the system runs mainly
# MySQL.
#
# You can copy this file to
# /etc/my.cnf to set global options,
# mysql-data-dir/my.cnf to set server-specific options (in this
# installation this directory is /usr/local/mysql/data) or
# ~/.my.cnf to set user-specific options.
#
# In this file, you can use all long options that a program supports.
# If you want to know which options a program supports, run the program
# with the "--help" option.

# The following options will be passed to all MySQL clients
[client]
#password       = your_password
port            = 3306
socket          = /tmp/mysql.sock

# Here follows entries for some specific programs

# The MySQL server
[mysqld]
datadir         = /usr/local/mysql/data
port            = 3306
socket          = /tmp/mysql.sock

skip-slave-start
skip-locking
skip-name-resolve
key_buffer = 384M
max_allowed_packet = 128M
max_connections = 9999
table_cache = 32000
open_files_limit = 65000
sort_buffer_size = 2M
read_buffer_size = 2M
read_rnd_buffer_size = 8M
myisam_sort_buffer_size = 64M
thread_cache = 8
query_cache_size = 32M
thread_concurrency = 16
max_heap_table_size=300000000
interactive_timeout = 288000
wait_timeout = 288000
log-slow-queries = slow_queries.log
long_query_time = 1
log-bin = mysql-bin
log-bin-index = mysql-bin.index
expire_logs_days = 7
log-slave-updates

# required unique id between 1 and 2^32 - 1
# defaults to 1 if master-host is not set
# but will not function as a master if omitted
server-id       = 2
relay-log = relay-bin
relay-log-index = relay-bin.index
relay_log_purge = 1
#innodb_data_home_dir = /usr/local/mysql/data/
#innodb_data_file_path = ibdata1:2000M;ibdata2:10M:autoextend
#innodb_log_group_home_dir = /usr/local/mysql/data/
#innodb_log_arch_dir = /usr/local/mysql/data/
# You can set .._buffer_pool_size up to 50 - 80 %
# of RAM but beware of setting memory usage too high
#innodb_buffer_pool_size = 384M
#innodb_additional_mem_pool_size = 20M
# Set .._log_file_size to 25 % of buffer pool size
#innodb_log_file_size = 100M
#innodb_log_buffer_size = 8M
#innodb_flush_log_at_trx_commit = 1
#innodb_lock_wait_timeout = 50

innodb_log_group_home_dir = /usr/local/mysql/data
innodb_log_arch_dir = /usr/local/mysql/data
innodb_buffer_pool_size = 1G
innodb_additional_mem_pool_size = 25M
innodb_log_file_size = 250M
innodb_log_buffer_size = 50M
innodb_flush_log_at_trx_commit = 0
innodb_lock_wait_timeout = 90
innodb_thread_concurrency=8

[mysqldump]
quick
max_allowed_packet = 16M

[mysql]
no-auto-rehash
# Remove the next comment character if you are not familiar with SQL
#safe-updates

[isamchk]
key_buffer = 256M
sort_buffer_size = 256M
read_buffer = 2M
write_buffer = 2M

[myisamchk]
key_buffer = 256M
sort_buffer_size = 256M
read_buffer = 2M
write_buffer = 2M

[mysqlhotcopy]
interactive-timeout

[mycheckpoint]
purge-days = 365
disable_bin_log
