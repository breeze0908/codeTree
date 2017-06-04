#!/bin/bash
if [ -e /etc/rsyncd.conf ]
then
echo exist
exit
fi
if [ ! -d /etc/rsyncd/ ]; then
	mkdir /etc/rsyncd/
fi
mkdir -p  /var/log/rsync
touch   /var/log/rsync.lock
cat >/etc/rsyncd.conf <<CONF
uid = root
gid = root
port = 3873
use chroot = no
max connections = 4000
timeout=600
#syslog facility = local5
pid file = /var/log/rsync/rsyncd.pid
log file = /var/log/rsync/rsync.log
lock file= /var/log/rsync/rsync.lock

[kankan]
        path = /data/vhosts
        refuse options =  delete
        comment = tel-cdn
        read only= no
       # ignore errors
        auth users = kankan
        secrets file = /etc/rsyncd/rsyncd.secrets
CONF
yum install -y rsync xinetd
#sed -i s/"disable\t= yes"/'disable = no'/ /etc/xinetd.d/rsync

echo "kankan:D2UkOsZT6N" >/etc/rsyncd/rsyncd.secrets
chmod 600 /etc/rsyncd/rsyncd.secrets

sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
sed -i "/COMMIT/d" /etc/sysconfig/iptables
echo '-A RH-Firewall-1-INPUT -m state  --state NEW -m tcp -p tcp --dport 3873 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
echo "/usr/bin/rsync --daemon" >>/etc/rc.local

/usr/bin/rsync --daemon
service iptables restart


