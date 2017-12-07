<?php

define('APP_PATH', realpath(dirname(__FILE__)));

try {
	require APP_PATH . '/autoloader.php';
} catch (Exception $e) {
	echo $e->getMessage();
}
