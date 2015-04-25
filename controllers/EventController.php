<?php

namespace app\controllers;

use Yii;
use app\models\Event;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Fights;
use app\models\User;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
{

    public function behaviors()
    {
        return
		[
			'access' =>
			[
				'class' => AccessControl::className(),
				'only' => ['draw', 'run', 'create'],
				'rules' =>
				[
					[
						'actions' => ['create'], /* actions not needed if same rule applies to all listed in 'only'*/
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin(Yii::$app->user->identity->username);
						}
					],
					[
						'actions' => ['draw'], /* multiple rules can be specified, one per action (?) */
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin(Yii::$app->user->identity->username);
						}
					],
					[
						'actions' => ['run'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin(Yii::$app->user->identity->username);
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
	 * Display results of a completed event
	 * @param integer $id the ID of the event
	 * @return mixed
	 */
	public function actionResult($id)
	{
        $model = $this->findModel($id);
		
		return $this->render('result',
			[
				'model' => $model,
				'teams' => $model->getTeams($id),
			]);
	}
	
	/**
	 * Start an event. Run the first fights and all the initial byes
	 * @param integer $id the ID of the event
	 * @return mixed
	 */
	public function actionRun($id)
	{
		$fights = new Fights();
		$count = 0;
		do
		{
			$status = $fights->runByes($id);
			$count += 1;
		} while ($status == true);
		$message = "Ran $count byes";
		return $this->actionDebug($id, 'Message', $message);
	}
	
	/**
	 * Start an event. Build the fights table and do the draw
	 * @param integer $id the ID of the event
	 * @return mixed
	 */
	public function actionDraw($id)
	{
		$fights = new Fights();
		$setupOK = Event::stateSetup($id);
		if ($setupOK == false)
		{
			$error = "Failed to save Setup state to event model";
			return $this->actionDebug($id, 'Error', $error);
		}

		/* get the teams array */
		$event = $this->findModel($id);
		$teams = $event->getTeams($id);
		/* calculate number of entrants for this event */
		$numEntrants = $event->getEntrants()->count();
		if ($numEntrants < 8 || $numEntrants > 128)
		{
			$error = "Number of entrants outside allowed range";
			return $this->actionDebug($id, 'Error', $error);
		}
		/* calculate required size of each group */
		$maxTeamSize = count(reset($teams));
		if ($maxTeamSize <= 2 && $numEntrants < 32)
		{
			$numGroups = 2;
		}
		else if ($maxTeamSize <= 4 && $numEntrants < 64)
		{
			$numGroups = 4;
		}
		else
		{
			$numGroups = 8;
		}
		/* assign robots to groups */
		$retVal = Event::assignGroups($teams, $numEntrants, $numGroups);
		if ($retVal[0] == 1)
		{
			/* can't fit team in remaining groups */
			$error = "Team size is bigger than number of spaces available";
			return $this->actionDebug($id, 'Error', $error);
		}
		$entrants = $retVal[1];
		
		/* create an array of robots per group */
		$groupList = array();
		foreach ($entrants as $robot => $group)
		{
			$groupList[$group][] = $robot;
		}
		
		/* add a new set of fights to the fights table */
		$fights->insertDoubleElimination($id);
		
		$fights->setupEvent($id, $groupList);
		
		/* ready to start! */
		$setupOK = Event::stateRunning($id);
		if ($setupOK == false)
		{
			$error = "Failed to save Running state to event model";
			return $this->actionDebug($id, 'Error', $error);
		}
		else
		{
			return $this->actionView($id);
		}
	}

	/**
	 * Render the debug view with a variable name and value
	 * @param integer $id Event ID
	 * @param string $debugName Name of variable to be dumped
	 * @param mixed $debugValue Value of variable to be dumped
	 * @return mixed
	 */
    public function actionDebug($id, $debugName, $debugValue)
    {
        $model = $this->findModel($id);
		
		return $this->render('debug',
		[
            'model' => $model,
			'debugName' => $debugName,
			'debugValue' => $debugValue,
        ]);
    }
	
    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider(
		[
            'query' => Event::find(),
        ]);

        return $this->render('index',
		[
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
		
		return $this->render('view',
		[
            'model' => $model,
			'teams' => $model->getTeams($id),
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Event();

        if ($model->load(Yii::$app->request->post()))
		{
			if ($model->save())
			{
				return $this->redirect(['view', 'id' => $model->id]);
			}
			else
			{
				return $this->render('debug',
				[
					'model' => $model,
					'debugName' => '$model->save() failed. Post data:',
					'debugValue' => Yii::$app->request->post(),
				]);
			}
		}
		else
		{
			return $this->render('create',
			[
				'model' => $model,
			]);
		}
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
		{
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
     * Deletes an existing Event model.
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
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null)
		{
            return $model;
        }
		else
		{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
