<?php

namespace app\controllers;

use Yii;
use app\models\Fights;
use app\models\Entrant;
use app\models\Event;
use app\models\FightsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
     * @param string $debugName Name of variable to be dumped
     * @param mixed $debugValue Variable to be dumped
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
     * Lists all Fights models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FightsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
        $model = $this->findModel($id);
		$request = Yii::$app->request;

		$winner = $request->get('winner');
		if ($winner == $model->robot1->id)
		{
			$loser = $model->robot2->id;
		}
		else if ($winner == $model->robot2->id)
		{
			$loser = $model->robot1->id;
		}
		else
		{
			$error = "Winner = $winner but does not match Robot1 $model->robot1->id or Robot2 $model->robot2->id";
			return $this->actionDebug($id, 'Error', $error);
		}

		$model->winnerId = $winner;
		$model->loserId = $loser;
		$model->save(false, ['winnerId', 'loserId']);

		$finished = true;
		if ($model->winnerNextFight > 0)
		{
			$finished = false;
			$model->updateNext($model->id, $model->winnerNextFight, $model->winnerId);
			$model->updateNext($model->id, $model->loserNextFight, $model->loserId);

			do
			{
				$status = $model->runByes($model->eventId);
			} while ($status == true);
        }
		if ($model->save())
		{
			$entrant = Entrant::findOne($loser);
			$entrant->status -= 1;
			if ($entrant->status == 0)
			{
				$entrant->finalFightId = $model->id - Event::findOne($model->eventId)->offset;
			}
			$entrant->save();
			if ($finished)
			{
				$entrant = Entrant::findOne($winner);
				$entrant->finalFightId = 255;
				$entrant->save();
				/* update event state */
				$event = Event::findOne($model->eventId);
				$event->state = 'Complete';
				$event->save(false, ['state']);
				/* announce results! */
				return $this->redirect(['event/result', 'id' => $model->eventId]);
			}
			else
			{
				return $this->redirect(['index', 'id' => $model->id]);
			}
        }
		else
		{
			$error = "Failed to save model to database";
            return $this->actionDebug($id, 'Error', $error);
        }
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
