# !/usr/bin/php


from tools import ip

print ip.__author__

ip = ip.get_local_ip_list()
print ip


import os
cmd = "LC_ALL=C ifconfig | grep 'inet addr:'| grep -v '127.0.0.1' | cut -d: -f2 | awk '{ print $1}'"
# result = os.system(cmd)
print cmd
result = os.popen(cmd).read()
ip_list = result.strip().split('\n')
print ip_list



from tools import file



