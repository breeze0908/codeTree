########安装redis###########
cd /usr/local/src
#wget  http://download.redis.io/releases/redis-3.0.5.tar.gz
tar xvf redis-3.0.5.tar.gz
mv redis-3.0.5 /usr/local/
cd /usr/local/redis-3.0.5/
make
mkdir data  logs 
cp /usr/local/redis-3.0.5/redis.conf /etc/redis.conf
echo "vm.overcommit_memory = 1" >>/etc/sysctl.conf
sysctl vm.overcommit_memory=1
sed -i "s/daemonize no/daemonize yes/" /etc/redis.conf
sed -i "/logfile/d" /etc/redis.conf
sed -i "/daemonize yes/a logfile \/usr\/local\/redis-3.0.5\/logs\/redis.log" /etc/redis.conf
sed -i "s/# syslog-enabled no/syslog-enabled no/" /etc/redis.conf
sed -i "s/dir .\//dir \/usr\/local\/redis-3.0.5\/data/" /etc/redis.conf
/usr/local/redis-3.0.5/src/redis-server /etc/redis.conf &
echo -e "\n"  >> /etc/rc.local
echo "/usr/local/redis-3.0.5/src/redis-server /etc/redis.conf &" >>/etc/rc.local
