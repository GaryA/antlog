<?php

namespace app\controllers;

use Yii;
use app\models\UpdateForm;
use app\models\User;
use app\models\Robot;
use app\models\Fights;
use app\models\Event;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\yii\data;

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
				'only' => ['delete', 'update'],
				'rules' =>
				[
					[
						'actions' => ['delete', 'update'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin();
						}
					],
					[
						'actions' => ['update'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isCurrentUser(Yii::$app->request->get('id'));
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
    	$robotsProvider = new ActiveDataProvider([
    		'query' => Robot::find()->where(['teamId' => $id])->orderBy(['classId' => SORT_DESC]),
    	]);
    	$query = Fights::getNextFights($id);
    	$nextFightsProvider = new ActiveDataProvider([
    		'query' => $query,
    		'sort'=> ['defaultOrder' => ['fightRound'=>SORT_ASC, 'fightBracket' => SORT_DESC, 'fightGroup' => SORT_ASC, 'fightNo' => SORT_ASC]]
    	]);
    	$userModel = User::findIdentity($id);
// TODO:
// Don't want to hard code event number,
// want to iterate over all events and produce another array dimension (?)
    	$eventsProvider = new ActiveDataProvider([
    		'query' => Event::find(),
    		]);
    	$events = $eventsProvider->getModels();
    	$robots = $robotsProvider->getModels();
    	foreach ($robots as $robot)
    	{
    		foreach ($events as $event)
    		{
    			$query = Fights::getCompleteFights($event->id, $robot->id);
    			$fightsProvider[$robot->name][$event->name] = new ActiveDataProvider([
    				'query' => $query,
    				'sort'=> ['defaultOrder' => ['fightRound'=>SORT_ASC, 'fightBracket' => SORT_DESC, 'fightGroup' => SORT_ASC, 'fightNo' => SORT_ASC]]
    			]);
    		}
    	}
        return $this->render('view', [
            'model' => $this->findModel($id),
        	'robots' => $robotsProvider,
        	'nextFights' => $nextFightsProvider,
        	'fights' => $fightsProvider,
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
        $updateModel = new UpdateForm();
        $userModel = User::findIdentity($id);
        $updateModel->setAttributes(
        [
        	'id' => $userModel->id,
        	'username' => $userModel->username,
        	'email' => $userModel->email,
        	'team_name' => $userModel->team_name,
        ], false);

        if ($updateModel->load(Yii::$app->request->post()))
        {
        	//$model->username = Yii::$app->request->post('User')['username'];
        	if ($userModel =  $updateModel->update($id))
			{
				Yii::$app->getSession()->setFlash('success', 'Updated user model.');
				return $this->redirect(['view', 'id' => $userModel->id]);
        	}
			else
			{
				Yii::$app->getSession()->setFlash('error', 'Failed to update user model.');
				return $this->render('update', ['model' => $updateModel]);
			}
        }
		else
		{
			return $this->render('update',
			[
                'model' => $updateModel,
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
     * Finds the User team model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
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
