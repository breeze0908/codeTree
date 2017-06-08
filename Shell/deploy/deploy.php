#! /usr/local/bin/php
<?php
	$shortopts = "";
	$longopts  = [
		"file::",
		"exclude-file::",
		"exclude::",
		"server::",
		"server-file::",
		"password-file::",
		"help::",
	];
	$options = getopt($shortopts, $longopts);

	if (isset($options['help'])) {
		display_help();
		exit;
	}

	// rsyncd root defined
	$server_root = '/data/vhosts/deploy/';
	$local_root  = '/var/www/deploy/';
	if (isset($options['file'])) {
		$local_root .= $options['file'];
		$server_root .= $options['file'];
	}

	// exclude option
	if (isset($options['exclude-file'])) {
		$default_exclude = ' --exclude-from=' . check_file($options['exclude-file']) . ' ';
	} else if (isset($options['exclude'])) {
		$default_exclude = ' --exclude=' . $options['exclude'];
	} else {
		$default_exclude = ' --exclude-from=' . check_file('./exclude_list.txt') . ' ';
	}

	// secret option
	$secrets_file = '/etc/rsyncd/rsync_KTr01155.secrets';
	if (isset($options['password-file'])) {
		if (!is_file($options['password-file'])) {
			exit('secret file :' . $options['password-file'] . ' does not exist');
		}
		$secrets_file = $options['password-file'];
	}

	// server option
	if (isset($options['server-file'])) {
		$sync_servers = parser_server_file($options['server-file']);
	} else if (isset($options['server'])) {
		$sync_servers = [$options['server']];
	} else {
		$sync_servers = parser_server_file('./server_list.txt');
	}

	// rsyncd
	foreach ($sync_servers as $server) {
		$server = trim($server);
		if (empty($server)) {
			continue;
		}

		$cmd = "rsync -avzrP {$default_exclude} {$local_root} --port=3873  kankan@{$server}::online{$server_root} --password-file={$secrets_file}";
		//echo $cmd;
		$result = system($cmd);
		echo 'server : "' . $server . '" rsync completed. ' . "\r\n";
	}

	/**
	 * 解析文件信息到数组
	 */
	function parser_server_file($filename) {
		if (!is_file($filename)) {
			exit('server-file "' . $filename . '" does not exist in system.' . "\r\n");
		}

		return file($filename);
	}

	function check_file($filename) {
		if (!is_file($filename)) {
			exit('exclude-file "' . $filename . '" does not exist in system.' . "\r\n");
		}

		return $filename;
	}

	/**
	 * 显示帮助信息
	 */
	function display_help() {
		echo 'deault: rsync -avzrP exclude-file=./exclude_list.txt /var/www/deploy/  --port=3873 kankan@{$server}::online/data/vhosts/deploy/ password-file=/etc/rsyncd/rsync_KTr01155.secrets.' . "\r\n" . '              --server-file=./server_list.txt' . "\r\n";
		echo ' --help  display all help options' . "\r\n";
		echo ' --file  specify the dir or file in the deploy dir' . "\r\n";
		echo ' --exclude-file  the file in the exclude file will not be rsync' . "\r\n";
		echo ' --exclude  specify the filename which will not be rsync' . "\r\n";
		echo ' --exclude-file  the file in the exclude file will not be rsync' . "\r\n";
		echo ' --server  specify the server which will be rsync' . "\r\n";
		echo ' --server-file  the server list in file will not be rsync' . "\r\n";
		echo ' --password-file  specify the password which will be used to rsync' . "\r\n";
}
