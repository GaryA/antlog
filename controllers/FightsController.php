<?php

namespace app\controllers;

use Yii;
use app\models\Fights;
use app\models\Entrant;
use app\models\Event;
use app\models\FightsSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

/**
 * FightsController implements the CRUD actions for Fights model.
 */
class FightsController extends Controller
{
    public function behaviors()
    {
        return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['create', 'update'],
				'rules' => [
					[
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
     * Render debug view to dump variable name & value
     * @param integer $id Model ID
     * @return mixed
     *
     * query params are name (label for variable), value (value of variable to dump)
     */
    public function actionDebug($id)
    {
        $model = $this->findModel($id);
        $params = Yii::$app->request->queryParams;

		return $this->render('debug', [
            'model' => $model,
			'debugName' => $params['name'],
			'debugValue' => $params['value'],
        ]);
    }

    /**
     * Lists all Fights models.
     * @param integer $eventId
     * @return mixed
     */
    public function actionIndex($eventId = NULL, $byes = 0, $complete = 1)
    {
    	if ($eventId == NULL)
    	{
        	$searchModel = new FightsSearch();
        	$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        	return $this->render('index', [
        	    'searchModel' => $searchModel,
        	    'dataProvider' => $dataProvider,
        	]);
    	}
    	else
    	{
    		$query = Fights::find()->where(['eventId' => $eventId]);
			if ($complete == 0)
    		{
    			// don't show completed fights
    			$query->andWhere(['winnerId' => -1]);
    		}
    		if ($byes == 0)
    		{
    			// only show fights where both robots are known
    			$query->andWhere(['>', 'robot1Id', 0])->andWhere(['>', 'robot2Id', 0]);
    		}
    		if ($byes == 1)
    		{
    			// only show fights where at least one robot is known
    			$query->andWhere(['or', 'robot1Id > 0', 'robot2Id > 0']);
    		}
    		$fightsProvider = new ActiveDataProvider([
    			'query' => $query,
    			'sort'=> ['defaultOrder' => ['fightRound'=>SORT_ASC, 'fightGroup' => SORT_ASC, 'fightBracket' => SORT_ASC, 'fightNo' => SORT_ASC]]
    		]);
    		if (!User::isUserAdmin())
    		{
    			// if user is not Admin, automatically reload the page every 30 seconds
    			Yii::$app->view->registerMetaTag(['http-equiv' => 'refresh', 'content' => '30']);
    		}
    		return $this->render('indexevent', [
    			'fightsProvider' => $fightsProvider,
    			'eventId' => $eventId,
    		]);
    	}
    }

    /**
     * Displays a single Fights model.
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
     * Creates a new Fights model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fights();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Fights model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$request = Yii::$app->request;
		$winner = $request->get('winner');

		$model = $this->findModel($id);
		$result = $model->updateCurrent($id, $winner);
		return $this->redirect($result);

    }

    /**
     * Deletes an existing Fights model.
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
     * Checks whether a fight can be undone (or the result changed)
     * @param string $id
     * @return mixed JSON
     */
    public function actionCheck($id)
    {
    	if(Yii::$app->request->isAjax)
    	{
    		$model = $this->findModel($id);
    		$retVal = $model->isOKToChange($id);
    		$pos = strpos($retVal, 'OK');
    		if ($pos === 0)
    		{
    			return '{"status":"true", "string":"' . $retVal . '"}';
    		}
    		else
    		{
    			if ($pos === false) $pos = 'false';
    			return '{"status":"false", "string":"' . $retVal . '"}';
    		}
    	}
    	else
    	{
    		return $this->redirect(['index']);
    	}
    }

    /**
     * Finds the Fights model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Fights the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fights::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
