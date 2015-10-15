<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'antlog3',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
		'urlManager' => [
    		'class' => 'yii\web\UrlManager',
			'showScriptName' => false,
			'enablePrettyUrl' => true,
			'rules' => [
				'<controller:\w+>/<id:\d+>' => '<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
			],
		],
    	'consoleRunner' => [
    		'class' => 'vova07\console\ConsoleRunner',
    		'file' => '@app/yii',
    		'php' => 'C:\xampp\php\php.exe'
    	],
    	'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'abcdefghijklmnopqrstuvwxyz012345',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
			'viewPath' => '@app/mail',
        	'transport' =>
        	[
        		// these settings must be made specifically for the server
        		'class' => 'Swift_SmtpTransport',
        		'host' => 'localhost',
        		'username' => 'username',
        		'password' => 'password',
        		'port' => '465',
        		'encryption' => 'tls',
        	]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
				[
					'class' => 'yii\log\FileTarget',
					'levels' => ['trace'],
					'logFile' => '@app/runtime/logs/trace.log',
					'maxFileSize' => 1024 * 4,
					'maxLogFiles' => 20,
				],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
