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
						'actions' => ['import'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin();
						}
					],
					[
                        'actions' => ['export'],
                        'allow' => true,
                        'roles' => ['@'],
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
				$db->importFile($model->savedFile);
				Yii::$app->getSession()->setFlash('success', 'Database updates imported.');
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
		$db->exportEnd();
		$db->fileDownload();

	    Yii::$app->getSession()->setFlash('success', 'User, Robot, Event, Entrant and Fights tables exported.');
	    return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}
}