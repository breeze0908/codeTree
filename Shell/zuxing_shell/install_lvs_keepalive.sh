#!/bin/bash
src_dir="/usr/local/src"
yum install gcc gcc-c++ -y
yum -y install kernel-devel


cd $src_dir
wget http://www.linuxvirtualserver.org/software/kernel-2.6/ipvsadm-1.24.tar.gz
tar xvf ipvsadm-1.24.tar.gz
kernel_dir=`ls /usr/src/kernels/`
ln -s /usr/src/kernels/$kernel_dir/ /usr/src/linux
cd ipvsadm-1.24
make
make install

cd $src_dir
yum -y install openssl-devel popt-devel libnl-devel
wget http://www.keepalived.org/software/keepalived-1.2.18.tar.gz
tar xvf keepalived-1.2.18.tar.gz
cd keepalived-1.2.18
./configure --prefix=/usr/local/keepalived-1.2.18
make && make install
cp /usr/local/keepalived-1.2.18/etc/rc.d/init.d/keepalived /etc/init.d/
cp /usr/local/keepalived-1.2.18/sbin/keepalived /usr/bin/
cp /usr/local/keepalived-1.2.18/etc/sysconfig/keepalived /etc/sysconfig/
mv /usr/local/keepalived-1.2.18/etc/keepalived/keepalived.conf /usr/local/keepalived-1.2.18/etc/
ln -s /usr/local/keepalived-1.2.18 /usr/local/keepalived
sed -i 's/daemon keepalived ${KEEPALIVED_OPTIONS}/daemon keepalived -D -f \/usr\/local\/keepalived\/etc\/keepalived.conf/' /etc/init.d/keepalived
chkconfig keepalived on
echo "/etc/init.d/keepalived start" >>/etc/rc.local

sed -i "/icmp-host-prohibited/d" /etc/sysconfig/iptables
sed -i "/COMMIT/d" /etc/sysconfig/iptables
echo "-A RH-Firewall-1-INPUT -m state --state NEW -m tcp -p tcp --dport 80 -j ACCEPT" >>/etc/sysconfig/iptables
echo "-A RH-Firewall-1-INPUT -p vrrp -j ACCEPT" >>/etc/sysconfig/iptables
echo '-A RH-Firewall-1-INPUT -j REJECT --reject-with icmp-host-prohibited
COMMIT' >> /etc/sysconfig/iptables
/etc/init.d/iptables restart
