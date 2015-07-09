<?php

// If the package is installed via composer with --no-dev then yii2-dev-env will not be included
// If the development packages are included then including dev-env.php will set the YII_DEBUG and
// YII_ENV constants to their development/debug values, otherwise they default to production values
$dev_env = __DIR__ . '/../../antlog/vendor/garya/yii2-dev-env/dev-env.php';
if(file_exists($dev_env))
{
	include $dev_env;
}

// Define environment
// 'web' for online "master" installation
// 'local' for event "slave" installation
if (!defined('ANTLOG_ENV')) define('ANTLOG_ENV', 'local');

require(__DIR__ . '/../../antlog/vendor/autoload.php');
require(__DIR__ . '/../../antlog/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../../antlog/config/web.php');

(new yii\web\Application($config))->run();
