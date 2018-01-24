<?php

//有时候浏览器禁用cookie或者禁止第三方cookie的写入时，我们可能要使用到原生的session.

##1、test.php

ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0); //是否仅用cookie
ini_set('session.use_trans_sid', 1); //是否自动url带上session id
ini_set('session.name', 'sid'); //session name

session_start();
$_SESSION['name'] = 'hello';

echo '<a href="target.php?foo=bar">target</a> | <a href="target.php?' . session_name() . '=' . session_id() . '">target width session id</a>';

##发现url上自动带上session id。

## 2、target.php

ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 1);
ini_set('session.name', 'sid');

if (!isset($_GET[session_name()])) {
	die('fk');
}

session_id($_GET[session_name()]); // 要在start之前
session_start();

echo session_id();
echo '<br>';
print_r($_SESSION);