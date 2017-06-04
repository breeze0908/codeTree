<?php 
ini_set("date.timezone", "Asia/shanghai");
$server   = '127.0.0.1';
$database = 'xxx';
$username = 'root';
$password = '123456';

try {
	$db = new PDO('mysql:host='.$server.';dbname='.$database, $username, $password);
	$db->query("set names 'utf8'");
	$stmt = $db->prepare("select movieid from v_movie where movieid < 10");
	$stmt->execute();
	$result = $stmt->fetch();
	if(count($result) > 0 ) {
	   echo date('[ c ]', time()) . " | MySQL 链接正常...\n";
	}
}catch(Exception $e) {
	echo date('[ c ]', time()) . " | MySQL 链接错误...\n";
	print_r($e->getMessage());
	exec("ps aux | grep mysqld | grep -v grep | awk '{ print $2; }' | xargs kill -9; /etc/init.d/mysqld start");
}

