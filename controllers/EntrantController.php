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
				'only' => ['delete', 'update', 'create'],
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Render the signup view (not implemented)
     * @return mixed
     */
	public function actionSignup()
	{
		return $this->render('signup', [
            'dataProvider' => $dataProvider,
        ]);
	}

    /**
     * Lists all Entrant models.
     * @return mixed
     */
    public function actionIndex($eventId = NULL)
    {
   		if ($eventId == NULL)
   		{
    	$dataProvider = new ActiveDataProvider([
           	'query' => Entrant::find(),
       	]);
        return $this->render('index', [
            'dataProvider' => NULL,
        ]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'eventId' => $eventId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            	'event' => Event::findOne($eventId),
            ]);
        }
    }

    /**
     * Deletes an existing Entrant model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @param integer $eventId
     * @return mixed
     */
    public function actionDelete($id, $eventId)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index', 'eventId' => $eventId]);
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
        if (($model = Entrant::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
