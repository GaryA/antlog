<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class DbController extends Controller
{
	public function behaviors()
	{
		return
		[
			'access' =>
			[
				'class' => AccessControl::className(),
				'only' => ['import', 'export'],
				'rules' =>
				[
					[
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin();
						}
					],
				],
			]
		];
	}

	/**
	 * Import database from .sql file
	 * @return mixed
	 */
	public function actionImport()
	{
	    Yii::$app->getSession()->setFlash('error', 'Import of data is not yet implemented!');
		return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}

	/**
	 * Export database to .sql file
	 * @return mixed
	 */
	public function actionExport()
	{
	    $username = Yii::$app->db->username;
	    $password = Yii::$app->db->password;
	    preg_match('/dbname=(.+)/', Yii::$app->db->dsn, $matches);
	    $database = $matches[1];
	    $prefix = Yii::$app->db->tablePrefix;
	    $tables =
	    	$prefix . 'user' . ' ' .
	    	$prefix . 'robot' . ' ' .
	    	$prefix . 'event' . ' ' .
	    	$prefix . 'entrant' . ' ' .
	    	$prefix . 'fights';
	    $filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $database . '_' . date("Y-m-d-H-i-s") . '.sql';
	    if ($password == '')
	    {
	    	$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $username . ' ' . $database . ' ' . $tables . ' > ' . $filename;
	    }
	    else
	    {
	    	$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $username . ' -p' . $password . ' ' . $database . ' ' . $tables . ' > ' . $filename;
	    }
	    if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32')
	    {
	    	pclose(popen('start /b ' . $cmd, 'r'));
	    }
	    else
	    {
	    	pclose(popen($cmd, 'r'));
	    }
	    Yii::$app->getSession()->setFlash('success', 'User, Robot, Event, Entrant and Fights tables dumped to ' . $filename);
	    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}
}