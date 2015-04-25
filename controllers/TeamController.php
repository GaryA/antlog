<?php

namespace app\controllers;

use Yii;
use app\models\Team;
use app\models\SignupForm;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TeamController implements the CRUD actions for Team model.
 */
class TeamController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Team models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Team::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Team model.
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
     * Creates a new Team model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $teamModel = new Team();
        $signupModel = new SignupForm();

        if ($teamModel->load(Yii::$app->request->post()) && $teamModel->save())
		{
			if ($signupModel->load(['username' => $teamModel->name, 'password' => 'password'], ''))
			{
				if ($user = $signupModel->signup())
				{
					if (Yii::$app->getUser()->login($user))
					{
						//return $this->goHome();
						return $this->redirect(['view', 'id' => $teamModel->id]);
					}
					$message = 'Login failed';
					return $this->actionDebug($teamModel->id, 'Message', $message);
				}
				$message = 'Signup failed';
				return $this->actionDebug($teamModel->id, 'Message', $message);
			}
			$message = 'Loading model failed';
			return $this->actionDebug($teamModel->id, 'Message', $message);
        }
		else
		{
            return $this->render('create', [
                'model' => $teamModel,
            ]);
        }
    }

    /**
     * Updates an existing Team model.
	 * Renames the associated user too.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$userModel = User::findByUsername($model->name);

        if ($model->load(Yii::$app->request->post()) && $model->save())
		{
			$userModel->username = (Yii::$app->request->post('Team')['name']);
			$userModel->save();
			return $this->redirect(['view', 'id' => $model->id]);
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
     * Deletes an existing Team model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$userModel = User::findByUsername($model->name);

        $this->findModel($id)->delete();
		$userModel->delete();
		
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
        if (($model = Team::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
