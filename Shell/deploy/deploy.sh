#!/bin/bash
rsync -avzrP --exclude-from=exclude_list.txt --port=3873 /var/www/deploy/ kankan@27.148.182.42::online/data/vhosts/deploy --password-file=/etc/rsyncd/rsync_KTr01155.secrets
