#!/bin/bash

src_dir="/usr/local/src"
srs_dir="/usr/local/sandai/"
yum install wget gcc gcc-c++ ncurses* openssl openssl-devel -y


#############install python-2.7.6####################
cd $src_dir
old_version=`python -V`
wget https://www.python.org/ftp/python/2.7.6/Python-2.7.6.tgz
tar xzvf Python-2.7.6.tgz
cd Python-2.7.6
./configure --prefix=/usr/local/Python-2.7.6
make && make install
#mv /usr/bin/python /usr/bin/python2_$old_num
sed -i '/export PATH USER LOGNAME MAIL HOSTNAME HISTSIZE HISTCONTROL/ i\PythonPATH=\/usr\/local\/Python-2.7.6\/bin \nPATH=\$PythonPATH:\$PATH' /etc/profile
source /etc/profile
#####################################################

cd $src_dir
wget https://pypi.python.org/packages/0d/13/ce6a0a22220f3da7483131bb8212d5791a03c8c3e86ff61b2c6a2de547cd/setuptools-26.0.0.tar.gz#md5=846e21fea62b9a70dfc845d70c400b7e --no-check-certificate
tar xvf setuptools-26.0.0.tar.gz
cd setuptools-26.0.0
python setup.py install

#pip-8.1.2只支持python2.7.9 或者以上的。
cd $src_dir
wget https://pypi.python.org/packages/e7/a8/7556133689add8d1a54c0b14aeff0acb03c64707ce100ecd53934da1aa13/pip-8.1.2.tar.gz#md5=87083c0b9867963b29f7aba3613e8f4a --no-check-certificate
#wget --no-check-certificate https://github.com/pypa/pip/archive/1.5.5.tar.gz
tar xvf pip-8.1.2.tar.gz
cd $src_dir/pip-8.1.2
python setup.py install



#版本不对时，要先卸载
#pip uninstall setuptools -y
#pip uninstall pip -y
#https://github.com/pypa/pip/releases  pip版本下载网站