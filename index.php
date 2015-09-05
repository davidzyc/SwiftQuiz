<?php

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('APP_DEBUG',false);

$_GET['m'] = 'Home';
$_GET['c'] = 'User';

define('DIR_SECURE_FILENAME','index.html');

define('APP_PATH','./SwiftQuiz/');

require './ThinkPHP/ThinkPHP.php';

?>