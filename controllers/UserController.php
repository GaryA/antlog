<?php

namespace app\controllers;

use Yii;
use app\models\UpdateForm;
use app\models\User;
use app\models\Robot;
use app\models\Fights;
use app\models\Event;
use app\models\Lock;
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
				'only' => ['create', 'delete', 'update'],
				'rules' =>
				[
					[
						'actions' => ['delete', 'update'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin() || User::isCurrentUser(Yii::$app->request->get('id'));
						}
					],
					[
						'actions' => ['create'],
						'allow' => true,
						'roles' => ['?'],
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

    	$eventsProvider = new ActiveDataProvider([
    		'query' => Event::find(),
    		]);
    	$events = $eventsProvider->getModels();
    	$robots = $robotsProvider->getModels();
    	$fightsProvider = null;
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
        if ($userModel == null)
        {
        	return $this->redirect('index');
        }
        else
        {
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
    }

    /**
     * Deletes an existing User team model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
    /**
     * Deletes existing User team data, but maintains links with robots that are already in the results table.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
     	$model = $this->findModel($id);
     	$robotProvider = new ActiveDataProvider(['query' => Robot::find()->where(['teamId' => $id])]);
     	$robots = $robotProvider->getModels();
     	foreach ($robots as $robot)
     	{
     		if ($robot->isOKToDelete($robot->id))
     		{
     			$robot->delete();
     		}
     		else if ($robot->isOKToRetire($robot->id))
     		{
     			$robot->active = false;
     			$robot->update();
     		}
     	}
    	if ($model->isTeamEmpty($id))
    	{
     		$model->delete();
    	}
    	else
    	{
    		$model->username = 'Deleted User ' . $id;
    		$model->password_hash = '0';
    		$model->password_reset_token = NULL;
    		$model->email = 'deleted@garya.org.uk';
    		$model->status = User::STATUS_DELETED;
    		$model->team_name = 'Deleted Team (' . $id . ')';
    		$model->update();
    	}
        return $this->redirect(['index']);
    }

    /**
     * Displays details of an active user
     * @param string $id
     * @return mixed
     */
    public function actionDetails($id)
    {
    	return $this->redirect(['/user/update/' . $id,
    			'model' => $this->findModel($id),
    		]);
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
