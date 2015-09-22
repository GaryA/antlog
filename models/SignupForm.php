<?php
namespace app\models;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $team_name;
    public $captcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Username "{value}" has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

        	['email', 'filter', 'filter' => 'trim'],
        	['email', 'required'],
       		['email', 'email'],

        	['team_name', 'filter', 'filter' => 'trim'],
        	['team_name', 'required'],
            ['team_name', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Team name "{value}" has already been taken.'],
        	['team_name', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 5],

        	['captcha', 'captcha'],
        	['captcha', 'required'],
        ];
    }

    public function attributeLabels()
    {
    	return
    	[
    			'id' => 'ID',
    			'username' => 'Username',
    			'email' => 'Email Address',
    			'team_name' => 'Team',
    	];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if validation fails
     */
    public function signup()
    {
        if ($this->validate())
		{
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->team_name = $this->team_name;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save(false);
			return $user;
        }

        return null;
    }
}
