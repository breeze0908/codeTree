#!/bin/bash
type=vod;
if [ -e /usr/local/sandai/distserver_flv ]
then
type=flv
fi

i=1
cp /etc/fstab /etc/fstab_1125
config_file=/usr/local/sandai/distserver_flv/conf/distserver.conf
sed -i /distserver.pub.dir.num=/d  $config_file
for dev_name in {b..l}
do
num=sd$dev_name\1
parted /dev/sd$dev_name <<EOF
mklabel
gpt
yes
mkpart
$num
ext4
1
4000G
q
EOF
	#mkfs.ext4 /dev/$num 
	mkfs.ext4 -T largefile /dev/$num
	if [ ! -d /data$i ];then
		mkdir /data$i
	fi
	echo "/dev/$num               /data$i          ext4            defaults,noatime                1 2" >> /etc/fstab
	let i++
done
mount -a


