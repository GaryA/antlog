<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\Robot;
use app\models\Event;
use app\models\User;

use yii\data\ActiveDataProvider;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class SiteController extends Controller
{

	public function behaviors()
    {
        return
		[
            'access' =>
			[
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' =>
				[
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
					[
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' =>
			[
                'class' => VerbFilter::className(),
                'actions' =>
				[
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return
		[
            'error' =>
			[
                'class' => 'yii\web\ErrorAction',
            ],
  			'captcha' =>
			[
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],

        ];
    }

    /**
     * Render the index view
     * @return mixed
     */
    public function actionIndex()
    {
        $teamData = new ActiveDataProvider(
		[
            'query' => User::find()->where(['user_group' => User::ROLE_TEAM]),
        ]);

    	$eventData = new ActiveDataProvider(
		[
            'query' => Event::find(),
			'sort'=> ['defaultOrder' => ['eventDate'=>SORT_DESC]]
        ]);

    	$robotData = new ActiveDataProvider(
		[
            'query' => Robot::find(),
        ]);

        return $this->render('index',
		[
            'robotData' => $robotData,
			'eventData' => $eventData,
			'teamData' => $teamData,
        ]);
    }

    /**
     * Handle the signup action
     * @return \yii\web\Response
     */
    public function actionSignup()
    {
    	$model = new SignupForm();
    	if ($model->load(Yii::$app->request->post()))
    	{
    		if ($user = $model->signup())
    		{
    			if (Yii::$app->getUser()->login($user))
    			{
    				return $this->goHome();
    			}
    		}
    	}
        return $this->render('signup',
        		[
            		'model' => $model,
        		]);
    }
    /**
     * Handle the login action
     * @return \yii\web\Response|mixed
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest)
		{
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login())
		{
            return $this->goBack();
        }
		else
		{
            return $this->render('login',
			[
                'model' => $model,
            ]);
        }
    }

    /**
     * Handle the logout action
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
	 * Render the about view
	 * @return mixed
	 */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Handle the password reset request
     * @return \yii\web\Response|mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
		{
            if ($model->sendEmail())
			{
				if (Yii::$app->mailer->useFileTransport == false)
				{
                	Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');
				}
                else
                {
                	Yii::$app->getSession()->setFlash('success', "Check password reset file in server's runtime folder.");
                }
                return $this->goHome();
            }
			else
			{
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for username provided.');
            }
        }

        return $this->render('requestPasswordResetToken',
		[
            'model' => $model,
        ]);
    }

    /**
     * Reset password
     * @param string $token Reset token
     * @throws BadRequestHttpException
     * @return \yii\web\Response|mixed
     */
    public function actionResetPassword($token)
    {
        try
		{
            $model = new ResetPasswordForm($token);
        }
		catch (InvalidParamException $e)
		{
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword())
		{
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword',
		[
            'model' => $model,
        ]);
    }
}
