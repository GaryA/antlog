<?php

namespace app\controllers;

use Yii;
use app\models\Event;
use app\models\Fights;
use app\models\User;
use app\models\ProgressBar;
use app\models\Entrant;
use app\models\Lock;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

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
				'only' => ['open', 'run', 'draw', 'setup', 'create', 'update', 'delete'],
				'rules' =>
				[
					[
						'actions' => ['run', 'draw', 'setup'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							return User::isUserAdmin();
						}
					],
					[
						'actions' => ['create'],
						'allow' => true,
						'roles' => ['@'],
					],
					[
						'actions' => ['delete'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							$id = Yii::$app->request->get('id');
							$model = $this->findModel($id);
							return ((User::isUserAdmin() || $model->organiserId == Yii::$app->user->id) && $model->isOKToDelete($id));
						},
					],
					[
						'actions' => ['update'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							$id = Yii::$app->request->get('id');
							$model = $this->findModel($id);
							$validState = $model->state == 'Registration' || $model->state =='Future';
							$validUser = User::isUserAdmin() || $model->organiserId == Yii::$app->user->id;
							return ($validUser && $validState);
						}
					],
					[
						'actions' => ['open'],
						'allow' => true,
						'roles' => ['@'],
						'matchCallback' => function($rule, $action)
						{
							$id = Yii::$app->request->get('id');
							$model = $this->findModel($id);
							$validState = $model->state == 'Closed' || $model->state =='Future';
							$validUser = User::isUserAdmin() || $model->organiserId == Yii::$app->user->id;
							return ($validUser && $validState);
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

    public function actions()
    {
    	return array(
    		'GetProgressBarData'=>array(
    			'class'=>'ActionGetProgressBarData',
    		),
    	);
    }

    /**
     * Get progress and return as JSON data
     * @param string $key identifier for this progress bar
     */
    public function actionGetProgressBarData($key)
    {
    	if(Yii::$app->request->isAjax)
    	{
    		$response = ProgressBar::get($key);
    		echo json_encode($response);
    	}
    }

    /**
     * Force event to complete state
     * @param integer $id the ID of the event
     * @return mixed
     */
    public function actionComplete($id)
    {
    	$model = $this->findModel($id);
    	$model->stateComplete($id);
    	return $this->redirect(['view', 'id' => $model->id]);
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
	 * Open event registration
	 * @param integer $id
	 * @return \app\controllers\mixed
	 */
	public function actionOpen($id)
	{
		$model = $this->findModel($id);
		$lock = New Lock;
		if ($model->state == 'Closed')
		{
			$lock->unlock();
		}
		$model->stateRegistration($id);
		return $this->redirect(['view', 'id' => $model->id]);
	}

	/**
	 * Start an event. Run the first fights and all the initial byes
	 * @param integer $id the ID of the event
	 * @return mixed
	 */
	public function actionRun($eventId)
	{
		if(Yii::$app->request->isAjax)
		{
			$model = $this->findModel($eventId);
			$model->stateRunning($eventId);
			/* redirect to fights page when complete */
			$redirect = "\"../fights/index?eventId=$eventId&byes=1&complete=0\"";
			$postId = $this->createPostFile();
			Yii::$app->consoleRunner->run("event/run $postId $eventId $redirect");
			return '{"status":"OK"}';
		}
	}

	/**
	 * Start an event. Build the fights table and do the draw
	 * @param integer $eventId the ID of the event
	 * @return mixed
	 */
	public function actionDraw($eventId)
	{
		if(Yii::$app->request->isAjax)
		{
			/* redirect to current page when complete */
			$redirect = '';
			/* get the teams array */
			$event = $this->findModel($eventId);
			$teams = $event->getTeams($eventId);
			$entrantsOK = Entrant::checkEntries($eventId);
			/* calculate number of entrants for this event */
			$numEntrants = $event->getEntrants()->count();
			if ($numEntrants < 8 || $numEntrants > 128)
			{
				Yii::$app->getSession()->setFlash('error', 'Number of entrants outside allowed range.');
			}
			else if ($entrantsOK == false)
			{
				Yii::$app->getSession()->setFlash('error', 'Some robots that are signed up have not been entered.<br>They must be entered or deleted before the draw can be done.');
			}
			else
			{
				/* change state to setup */
				$setupOK = $event->stateSetup($eventId);
				if ($setupOK == false)
				{
					Yii::$app->getSession()->setFlash('error', 'Failed to save Setup state to event model.');
				}
				else
				{
					$postId = $this->createPostFile();
					Yii::$app->consoleRunner->run("event/setup $postId $eventId $numEntrants $redirect");
					return '{"status":"OK"}';
				}
			}
			return '{"status":"Error"}';
		}
		else
		{
			return $this->actionView($eventId);
		}
	}

	/**
	 * Set up an event (only needed if the first attempt at a draw fails)
	 * @param integer $eventId
	 * @return view
	 */
	public function actionSetup($eventId)
	{
		if(Yii::$app->request->isAjax)
		{
			/* redirect to current page when complete */
			$redirect = '';
			/* get the teams array */
			$event = $this->findModel($eventId);
			$teams = $event->getTeams($eventId);
			/* calculate number of entrants for this event */
			$numEntrants = $event->getEntrants()->count();
			$postId = $this->createPostFile();
			Yii::$app->consoleRunner->run("event/setup $postId $eventId $numEntrants $redirect");
			return '{"status":"OK"}';
		}
		else
		{
			return $this->actionView($eventId);
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
			'sort'=> ['defaultOrder' => ['eventDate'=>SORT_DESC]]
        ]);

        return $this->render('index',
		[
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model with an error message.
     * @param string $id
     * @return mixed
     */
    public function actionError($id, $message)
    {
    	$model = $this->findModel($id);
    	Yii::$app->getSession()->setFlash('error', $message);

    	return $this->redirect(['event/view', 'id' => $id]);
    }

    /**
     * Displays a single Event model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
   		$model = $this->findModel($id);

       	if ($model->state == 'Future')
       	{
       		if ($model->eventDate == date('Y-m-d'))
       	    {
       			$model->stateRegistration($id);
       			// reload model to ensure updated state is shown
       			$model = $this->findModel($id);
       	    }
       	}

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
        	if ($model->eventDate == date('Y-m-d'))
        	{
        		$model->state = 'Registration';
        	}
        	// If an event is created locally, start with online registration closed
        	// Local registration is open until the draw is done
        	if (Yii::$app->params['antlog_env'] == 'local')
        	{
        		$model->state = 'Closed';
        	}
        	if ($model->save())
			{
				return $this->redirect(['view', 'id' => $model->id]);
			}
        }
		return $this->render('create',
		[
			'model' => $model,
			'userId' => Yii::$app->user->id,
		]);
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

    /**
     * Creates a file containing the post data, for use by the CLI script
     * @return string The post file ID
     */
    protected function createPostFile()
    {
    	$post = Yii::$app->request->post();
    	$postId = uniqid();
    	$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
    	file_put_contents($filename, json_encode($post));
    	return $postId;
    }
}
