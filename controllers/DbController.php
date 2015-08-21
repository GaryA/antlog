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
						'matchCallback' => function($rule, $action)
						{
							return (User::isUserAdmin() || Yii::$app->params['antlog_env'] == 'web');
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

        if (Yii::$app->request->isAjax)
        {
            $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
            if ($model->upload())
            {
            	$html = <<< END_OF_TEXT

END_OF_TEXT;
				return json_encode(['fileName' => $model->savedFile, 'html' => $html, 'status' => 'OK']);
            }
            return '{"status": "Error"}';
        }
        return $this->render('/site/upload', ['model' => $model]);
	}

	public function actionProcess()
	{
		if (Yii::$app->request->isAjax)
		{
			$filename = Yii::$app->request->post('filename');
			/* redirect to home page when complete */
			$redirect = "../index.php";
			$postId = $this->createPostFile();
			Yii::$app->consoleRunner->run("db/import $postId \"$filename\" $redirect");
			return '{"status":"OK"}';
		}
		//return $this->render('/db/process', ['fileName' => $fileName]);
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

		return;
		// Setting a flash and redirecting doesn't work with an inline file download.
	    // Yii::$app->getSession()->setFlash('success', 'User, Robot, Event, Entrant and Fights tables exported.');
	    // return $this->redirect(Yii::$app->urlManager->createUrl('/site/index'));
	}

	/**
	 * Creates a file containing the post data, for use by the CLI script
	 * @return string The post file ID
	 */
	protected function createPostFile()
	{
		$post = Yii::$app->request->post();
		$postId = uniqid();
		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
		file_put_contents($filename, json_encode($post));
		return $postId;
	}
}