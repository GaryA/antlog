<?php

namespace app\controllers;

use Yii;
use app\models\Entrant;
use app\models\Event;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * EntrantController implements the CRUD actions for Entrant model.
 */
class EntrantController extends Controller
{
    public function behaviors()
    {
        return [
			'access' =>
			[
				'class' => AccessControl::className(),
				'only' => ['delete', 'update', 'create', 'enter', 'signup'],
				'rules' =>
				[
					[
						'actions' => ['update', 'create', 'enter'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin();
						}
					],
					[
						'actions' => ['signup', 'delete'],
						'allow' => true,
						'roles' => ['@'],
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
     * Render the signup view
     * @return mixed
     */
	public function actionSignup($eventId)
	{
		$model = new Entrant;
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect(['index', 'eventId' => $eventId]);
		}
		else
		{
			return $this->render('signup', [
				'model' => $model,
				'event' => Event::findOne($eventId),
			]);
		}
	}

    /**
     * Lists all Entrant models.
     * @return mixed
     */
    public function actionIndex($eventId = NULL)
    {
   		if ($eventId == NULL)
   		{
			throw new NotFoundHttpException('The requested page does not exist.');
   		}
   		else
   		{
   			$entrantProvider = new ActiveDataProvider([
   				'query' => Entrant::find()->where(['eventId' => $eventId]),
   				'sort'=> ['defaultOrder' => ['group_num'=>SORT_ASC]]
   			]);
   			return $this->render('indexevent', [
   				'entrantProvider' => $entrantProvider,
   				'event' => Event::findOne($eventId),
   			]);
   		}
    }

    /**
     * Displays a single Entrant model.
     * @param string $id
     * @param integer $eventId
     * @return mixed
     */
    public function actionView($id, $eventId = NULL)
    {
        if ($eventId == NULL)
        {
        	return $this->render('view', [
        		'model' => $this->findModel($id),
        	]);
        }
        else
        {
			return $this->render('viewevent', [
				'model' => $this->findModel($id),
				'event' => Event::findOne($eventId),
			]);
		}
    }

    /**
     * Creates a new Entrant model.
     * If creation is successful, the browser will be redirected to the entrants list for the current event.
     * @param integer $eventId
     * @return mixed
     */
    public function actionCreate($eventId)
    {
        $model = new Entrant();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'eventId' => $eventId]);
        } else {
            return $this->render('create', [
                'model' => $model,
            	'event' => Event::findOne($eventId),
            ]);
        }
    }

    /**
     * Updates an existing Entrant model.
     * If update is successful, the browser will be redirected to the entrants list for the current event.
     * @param string $id
     * @param integer $eventId
     * @return mixed
     */
    public function actionUpdate($id, $eventId)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            return $this->redirect(['index', 'eventId' => $eventId]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            	'event' => Event::findOne($eventId),
            ]);
        }
    }

    /**
     * Enters an entrant that has been signed up.
     * If update is successful, the browser will be redirected to the entrants list for the current event.
     * @param string $id
     * @return mixed
     */
    public function actionEnter($id)
    {
    	$model = $this->findModel($id);

    	$model->status = 2;
    	$model->touch('updated_at');
    	if (!$model->save(true, ['status']))
    	{
    		Yii::$app->getSession()->setFlash('error', 'Entrant status could not be saved to model.');
    		$return = '{"status":"Error","newhtml":""}';
    	}
    	else
    	{
    		$return = '{"status":"OK","newhtml":"Entered"}';
    	}
    	if (Yii::$app->request->isAjax)
    	{
    		return $return;
    	}
    	return $this->render('indexevent', ['event' => Event::findOne($model->eventId)]);
    }

    /**
     * Deletes an existing Entrant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
    	$model->delete();
    	if (Yii::$app->request->isAjax)
    	{
    		return '{"status":"OK"}';
    	}
        return $this->render('indexevent', ['event' => Event::findOne($model->eventId)]);
    }

    /**
     * Finds the Entrant model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Entrant the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Entrant::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
