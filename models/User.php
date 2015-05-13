<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\models\Robot;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * @property integer $user_group
 * @property string $team_name
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

	const ROLE_ADMIN = 1;
	const ROLE_TEAM = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return
		[
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
		[
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
			['user_group', 'default', 'value' => self::ROLE_TEAM],
			['user_group', 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_TEAM]],
        ];
    }

    /**
     * Return an array of user IDs and names to populate a dropdown box
     * Only returns users that are teams (not administrators)
     * @param string $id
     * @return array
     */
    public static function teamDropdown($id = NULL)
    {
    	if ($id != NULL)
    	{
    		$models = static::find()->where(['id' => $id])->all();
    	}
    	else
    	{
    		$models = static::find()->where(['user_group' => self::ROLE_TEAM])->all();
    	}
    	$dropdown = NULL;
    	foreach ($models as $model)
    	{
    		$dropdown[$model->id] = $model->team_name;
    	}
    	return $dropdown;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token))
		{
            return null;
        }

        return static::findOne(
		[
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token))
		{
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

	/**
	 * Check whether current user belongs to admin group
	 * @return boolean
	 */
	public static function isUserAdmin()
	{
		if ((!Yii::$app->user->isGuest) && (Yii::$app->user->identity->user_group == self::ROLE_ADMIN))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check whether ID (probably team ID) is current user
	 * @param integer $id The ID to be checked
	 * @return boolean
	 */
	public static function isCurrentUser($id)
	{
		if ((!Yii::$app->user->isGuest) && (Yii::$app->user->identity->id == $id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get all robots belonging to user (team)
	 * @return \yii\db\ActiveQuery
	 */
	public function getRobots()
	{
		return $this->hasMany(Robot::className(), ['teamId' => 'id']);
	}

	/**
	 * Return true if user's team contains no robots (so may be deleted)
	 * @param integer $id
	 * @return boolean
	 */
	public function isTeamEmpty($id)
	{
		return Robot::find()->where(['teamId' => $id])->count() > 0 ? false : true;
	}

}
