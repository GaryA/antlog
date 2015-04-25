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
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 5],
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
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save(false);

			// Add teams to the 'team' role for authorisation
			$auth = Yii::$app->authManager;
			$authorRole = $auth->getRole('team');
			$auth->assign($authorRole, $user->getId());

			return $user;
        }

        return null;
    }
}
