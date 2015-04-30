<?php

namespace app\controllers;

use Yii;
use app\models\Robot;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * RobotController implements the CRUD actions for Robot model.
 */
class RobotController extends Controller
{
    public function behaviors()
    {
        return [
			'access' =>
			[
				'class' => AccessControl::className(),
				'only' => ['create', 'update', 'delete'],
				'rules' =>
				[
					[
						'actions' => ['create'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return (!Yii::$app->user->isGuest);
							// return User::isUserAdmin();
						}
					],
					[
						'actions' => ['update'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							$id = Yii::$app->request->get('id');
							$model = $this->findModel($id);
							return ((User::isUserAdmin() || $model->isUser($model)) && $model->isOKToEdit($id));
						}
					],
					[
						'actions' => ['delete'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							$id = Yii::$app->request->get('id');
							$model = $this->findModel($id);
							return ((User::isUserAdmin() || $model->isUser($model)) && $model->isOKToDelete($id));
						}
					],
				],
			],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Robot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Robot::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Robot model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Robot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Robot();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Robot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Robot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Robot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Robot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Robot::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
