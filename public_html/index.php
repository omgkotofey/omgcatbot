<?php
/**
 * Front controller
 *
 */

require_once '../vendor/autoload.php';

define('APPROOT_DIR', dirname(__DIR__));

$localConfig = [];

if (file_exists(APPROOT_DIR . '/config.php')) {
	$localConfig = require(APPROOT_DIR . '/config.php');
}
else {
	die('config.php is missing.');
}

require(APPROOT_DIR . '/vendor/autoload.php');

$config = array_merge(
	require(APPROOT_DIR . '/app/config/bot.php'),
	$localConfig
);

$bot = \app\core\CatBot::create($config);
$bot->run();