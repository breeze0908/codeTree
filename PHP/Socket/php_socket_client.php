<?php
/**
 * 客户端代码
 *
 * $file client.php
 * @author tanda <tanda@wondershare.cn>
 * @since alt+t
 * @copyright 2017 (c) Wondershare Inc.
 */
set_time_limit(0);

$host   = "127.0.0.1";
$port   = 2046;

// 创建一个Socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create  socket\n");

// 连接socket
$connection = socket_connect($socket, $host, $port) or die("Could not connet server\n");
// 数据传送 向服务器发送消息
socket_write($socket, "hello socket") or die("Write failed\n");
while ($buff = socket_read($socket, 1024, PHP_NORMAL_READ)) {
	echo ("Response was:" . $buff . "\n");
}
socket_close($socket);