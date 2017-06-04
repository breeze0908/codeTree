#!/bin/bash
cd /usr/local/src
wget https://nodejs.org/dist/v4.3.1/node-v4.3.1-linux-x64.tar.xz
tar xvf node-v4.3.1-linux-x64.tar.xz -C /usr/local
cd /usr/local
mv node-v4.3.1-linux-x64 node-v4.3.1
ln -s /usr/local/node-v4.3.1/bin/npm /usr/bin/npm
ln -s /usr/local/node-v4.3.1/bin/node /usr/bin/node