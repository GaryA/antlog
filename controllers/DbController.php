<?php

namespace app\controllers;

use Yii;
use app\models\Db;
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
		$db = new Db;
		$db->importUsers();
		$db->importRobots();
		$db->importEvents();
		$db->importEntrants();
		$db->importFights();
		Yii::$app->getSession()->setFlash('error', 'Import of data is not yet implemented!');
	    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}

	/**
	 * Export database to .sql file
	 * @return mixed
	 */
	public function actionExport()
	{
		$db = new Db;
		$db->exportUsers();
		$db->exportRobots();
		$db->exportEvents();
		$db->exportEntrants();
		$db->exportFights();

	    Yii::$app->getSession()->setFlash('success', 'User, Robot, Event, Entrant and Fights tables dumped to ' . Yii::getAlias('@runtime') . ' folder.');
	    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}
}