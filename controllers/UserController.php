<?php

namespace app\controllers;

use Yii;
use app\models\SignupForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public function behaviors()
    {
        return
        [
			'access' =>
			[
				'class' => AccessControl::className(),
				'only' => ['delete'],
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
			],
        	'verbs' =>
        	[
               	'class' => VerbFilter::className(),
               	'actions' =>
        		[
                   	'delete' => ['post'],
               	],
           	],
        ];
    }

    /**
     * Lists all User team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['user_group' => User::ROLE_TEAM]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User team model.
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
     * Updates an existing User team model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost)
        {
        	$model->username = Yii::$app->request->post('User')['username'];
        
        	if ($model->save())
			{
				return $this->redirect(['view', 'id' => $model->id]);
        	}
        }
		else
		{
            return $this->render('update',
			[
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User team model.
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
     * Render the debug view, dumping variable name and value
     * @param integer $id Model ID
     * @param string $debugName Name of variable to be dumped
     * @param mixed $debugValue Value of variable to be dumped
     * @return mixed
     */
    public function actionDebug($id, $debugName, $debugValue)
    {
        $model = $this->findModel($id);
		
		return $this->render('debug', [
            'model' => $model,
			'debugName' => $debugName,
			'debugValue' => $debugValue,
        ]);
    }

    /**
     * Finds the Team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Team the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
