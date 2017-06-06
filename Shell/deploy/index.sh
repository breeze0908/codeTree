#!/bin/bash

SERVER_IP='27.148.182.42'

LOCAL_MAIN_DIR='/var/www/deploy/'
SERVER_MAIN_DIR='/data/vhosts/deploy/'

rsync -avzrP  --exclude-from=exclude_list.txt --port=3873 $LOCAL_MAIN_DIR  kankan@27.148.182.42::online/data/vhost/deploy --password-file=/etc/rsyncd/php_rsyncd.secrets
tt
