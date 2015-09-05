<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

$_GET['m'] = 'Home';
$_GET['c'] = 'User';

define('DIR_SECURE_FILENAME','index.html');

define('APP_PATH','./Quizhub/');

require './ThinkPHP/ThinkPHP.php';

?>