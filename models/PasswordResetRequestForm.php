<?php
namespace app\models;

use app\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'exist',
                'targetClass' => '\app\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with this username.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne(
        [
            'status' => User::STATUS_ACTIVE,
            'username' => $this->username,
        ]);

        if ($user)
        {
            if (!User::isPasswordResetTokenValid($user->password_reset_token))
            {
                $user->generatePasswordResetToken();
            }

            if ($user->save())
            {
            	return \Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->email)
                    ->setSubject('Password reset for ' . \Yii::$app->name)
                    ->send();

/*				$myFile = fopen(\Yii::getAlias('@app') . "/password-reset.txt", "w");
				if ($myFile !== false)
				{
					fwrite($myFile, 'User: ' . $user->username . "\r\n");
					fwrite($myFile, 'Token: ' . $user->password_reset_token . "\r\n");
					fclose($myFile);
					return true;
				}
*/
           }
        }
        return false;
    }
}
