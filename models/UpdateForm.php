<?php
namespace app\models;

use app\models\User;
use yii\base\Model;
use Yii;

/**
 * Update form
 */
class UpdateForm extends Model
{
    public $id;
	public $username;
    public $email;
    public $password;
    public $team_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],

        	['email', 'filter', 'filter' => 'trim'],
        	['email', 'required'],
       		['email', 'email'],

        	['team_name', 'filter', 'filter' => 'trim'],
        	['team_name', 'required'],
        	['team_name', 'string', 'min' => 2, 'max' => 255],

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
     * Updates user profile.
     *
     * @return User|null the saved model or null if validation fails
     */
    public function update($id)
    {
        if ($this->validate())
		{
            $user = User::findIdentity($id);
            $user->username = $this->username;
            $user->email = $this->email;
            $user->team_name = $this->team_name;
            $user->save(false);

			return $user;
        }

        return null;
    }
}
