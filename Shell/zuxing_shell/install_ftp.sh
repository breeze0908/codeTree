#!/bin/sh
cd /usr/local/src
wget http://thunder:xunlei@apt.sandai.net/tmp/pureftp/pure-ftpd-1.0.36.tar.gz
tar -xvf pure-ftpd-1.0.36.tar.gz
cd pure-ftpd-1.0.36
./configure --prefix=/usr/local/pure-ftpd-1.0.36 --with-everything
make
make install
mkdir -p /usr/local/pure-ftpd-1.0.36/etc/
cp ./configuration-file/pure-ftpd.conf /usr/local/pure-ftpd-1.0.36/etc/
cp ./configuration-file/pure-config.pl /usr/local/pure-ftpd-1.0.36/bin/
chmod a+x /usr/local/pure-ftpd-1.0.36/bin/pure-config.pl

cd /usr/local/
ln -s pure-ftpd-1.0.36 pure-ftpd
ln -s /usr/local/pure-ftpd-1.0.36/bin/pure-pw /usr/local/bin/pure-pw


echo "/usr/local/pure-ftpd-1.0.36/bin/pure-config.pl /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf" >> /etc/rc.d/rc.local
sed -i 's#MinUID                      100#MinUID                      98#g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/MaxClientsNumber            50/MaxClientsNumber            1000/g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/# TrustedGID                    100/TrustedGID                    99/g' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/MaxClientsPerIP             8/MaxClientsPerIP             200/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/AllowUserFXP                no/AllowUserFXP                yes/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/MaxDiskUsage               99/MaxDiskUsage               97/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -i 's/# PassivePortRange          30000 50000/PassivePortRange          30000 50000/g'  /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf
sed -ir '/\/etc\/pureftpd.pdb/a\PureDB                        /usr/local/pure-ftpd/etc/pureftpd.pdb' /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf

/usr/local/pure-ftpd-1.0.36/bin/pure-config.pl /usr/local/pure-ftpd-1.0.36/etc/pure-ftpd.conf



###################
sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
sed -i "/COMMIT/d" /etc/sysconfig/iptables
echo '#office
-A RH-Firewall-1-INPUT -s 116.7.233.118 -m multiport -p tcp --dport 21,30000:50000 -j ACCEPT
-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
service iptables restart

##################
chattr -i /etc/hosts.allow
echo 'pure-ftpd:116.7.233.118' >> /etc/hosts.allow
touch /usr/local/pure-ftpd-1.0.36/etc/pureftpd.passwd
chattr +i /etc/hosts.allow