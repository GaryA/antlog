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
     * Lists all Fights models.
     * @param integer $eventId
     * @return mixed
     */
    public function actionIndex($eventId = NULL, $byes = 0, $complete = 2)
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
    		$event = Event::findOne($eventId);
    		$startId = Fights::find()
    			->where(['eventId' => $eventId])
    			->andWhere(['>', 'robot1Id', 0])
    			->andWhere(['>', 'robot2Id', 0])
    			->orderBy('id')
    			->one()
    			->id;
    		$query = Fights::find()->where(['eventId' => $eventId]);
			if ($complete == 0)
    		{
    			// don't show completed fights
    			$query->andWhere(['winnerId' => -1]);
    		}
    		if ($complete == 1)
    		{
    			// skip initial rounds that are all byes
    			$query->andWhere(['>=', 'id', $startId]);
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
    			'sort'=> ['defaultOrder' => ['fightRound'=>SORT_ASC, 'fightBracket' => SORT_DESC, 'fightGroup' => SORT_ASC, 'fightNo' => SORT_ASC]]
    		]);
    		if (!User::isUserAdmin())
    		{
    			// if user is not Admin, automatically reload the page every 30 seconds
    			Yii::$app->view->registerMetaTag(['http-equiv' => 'refresh', 'content' => '30']);
    		}
    		return $this->render('indexevent', [
    			'fightsProvider' => $fightsProvider,
    			'eventId' => $eventId,
    			'state' => $event->state,
    			'byes' => $byes,
    			'complete' => $complete,
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
		$complete = $request->get('complete');
		$change = $request->get('change', false);
		$change = filter_var($change, FILTER_VALIDATE_BOOLEAN); // set value to proper boolean type
		$replacement = $request->get('replacement', 0);
		$model = $this->findModel($id);
		$result = $model->updateCurrent($id, $winner, $complete, $change, $replacement);
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
     * Creates files to show robot names for current fights
     */
    public function actionCreateFiles()
    {
    	if(Yii::$app->request->isAjax)
    	{
    		$fightid = Yii::$app->request->post('fightid');
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'fightid.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, $fightid);
    		fclose($file);
    		$robot1 = Yii::$app->request->post('robot1');
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'robot1.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, $robot1);
    		fclose($file);
    		$robot2 = Yii::$app->request->post('robot2');
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'robot2.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, $robot2);
    		fclose($file);
    		$team1 = Yii::$app->request->post('team1');
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'team1.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, $team1);
    		fclose($file);
    		$team2 = Yii::$app->request->post('team2');
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'team2.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, $team2);
    		fclose($file);
    	}
       	else
    	{
    		return $this->redirect(['index']);
    	}
    }

    /**
     * Doesn't actually delete files used to show robot names for current fights
     * just replaces the content with nothing. Avoids having to repeatedly create and delete files.
     */
    public function actionDeleteFiles()
    {
    	if(Yii::$app->request->isAjax)
    	{
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'robot1.txt';
    	  	$file = fopen($filename, 'w');
    		fwrite($file, '');
    		fclose($file);
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'robot2.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, '');
    		fclose($file);
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'team1.txt';
    	  	$file = fopen($filename, 'w');
    		fwrite($file, '');
    		fclose($file);
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'team2.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, '');
    		fclose($file);
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'fightid.txt';
    		$file = fopen($filename, 'w');
    		fwrite($file, '');
    		fclose($file);
    		return '{"status":"OK"}';
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
    
    /**
     * 
     */
    public function actionJson($eventId, $byes = 0, $complete = 0){
    		$event = Event::findOne($eventId);
    		$startId = Fights::find()
    			->where(['eventId' => $eventId])
    			->andWhere(['>', 'robot1Id', 0])
    			->andWhere(['>', 'robot2Id', 0])
    			->orderBy('id')
    			->one()
    			->id;
    		$query = Fights::find()->where(['eventId' => $eventId]);
			if ($complete == 0)
    		{
    			// don't show completed fights
    			$query->andWhere(['winnerId' => -1]);
    		}
    		if ($complete == 1)
    		{
    			// skip initial rounds that are all byes
    			$query->andWhere(['>=', 'id', $startId]);
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
    		
    		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'fightid.txt';
    		$fightId = file_get_contents($filename);
    		if($fightId){
        		$active_fight = Fights::find()->where(['id' => $fightId])->one();
        	}else{
        	    $active_fight = null;
    	    }
    		
    		$json_object = array(
    		    "next" => array(),
    		    "now" => array(),
		    );
		    
		    if($active_fight){
		        $json_object["now"] = array(
		            "robot1" => ($active_fight->robot1 ? $active_fight->robot1->robot->name : null),
		            "robot2" => ($active_fight->robot2 ? $active_fight->robot2->robot->name : null),
		            "team1" => ($active_fight->robot1 ? $active_fight->robot1->robot->team->team_name : null),
		            "team2" => ($active_fight->robot2 ? $active_fight->robot2->robot->team->team_name : null),
		        );
		        $query->andWhere(['<>', 'id', $active_fight->id]);
	        }
    		$query->orderBy( array('fightRound'=>SORT_ASC, 'fightBracket' => SORT_DESC, 'fightGroup' => SORT_ASC, 'fightNo' => SORT_ASC) );
    		foreach($query->all() as $fight){
    		    $fightObj = array(
                    "id" => $fight->id,
    		        "robot1" => ($fight->robot1 ? $fight->robot1->robot->name : null),
    		        "robot2" => ($fight->robot2 ? $fight->robot2->robot->name : null),
    		        "team1" => ($fight->robot1 ? $fight->robot1->robot->team->team_name : null),
    		        "team2" => ($fight->robot2 ? $fight->robot2->robot->team->team_name : null),
    		        "round_label" => Fights::labelRound($fight),
    		    );
    		    $json_object["next"][] = $fightObj;
    		}
    		echo json_encode($json_object);
    }
}
