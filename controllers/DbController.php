<?php

namespace app\controllers;

use Yii;
use app\models\Db;
use app\models\User;
use app\models\UploadForm;
use yii\web\UploadedFile;
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
        $model = new UploadForm();

        if (Yii::$app->request->isPost)
        {
            $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
            if ($model->upload())
            {
				$db = new Db;
				$db->fileUpload();
				$db->importUsers();
				$db->importRobots();
				$db->importEvents();
				$db->importEntrants();
				$db->importFights();
				Yii::$app->getSession()->setFlash('error', 'Import of data is not yet implemented!');
			    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
            }
        }
        return $this->render(Yii::$app->urlManager->createUrl('/site/upload'), ['model' => $model]);
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
		$db->fileDownload();

	    Yii::$app->getSession()->setFlash('success', 'User, Robot, Event, Entrant and Fights tables dumped to ' . Yii::getAlias('@runtime') . ' folder.');
	    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}
}