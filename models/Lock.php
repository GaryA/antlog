<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%lock}}".
 *
 * @property integer $id
 * @property string $name
 *
 */
class Lock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lock}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lockState', 'lockUserId', 'updated_at'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lockState' => 'State',
            'lockUserId' => 'User',
        	'updated_at' => 'Time'
        ];
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

    public function lock($userId)
    {
    	// id is always 1
    	// set lockState = 1, lockUserId = $userId, lockTime = now()
		$record = $this->findOne(1);
		$record->lockState = 1;
		$record->lockUserId = $userId;
		$record->update();
		$record->save();
    }

    public function unlock()
    {
    	// id is always 1
    	// set lockState = 0, lockUserId = NULL, lockTime = now()
		$record = $this->findOne(1);
		$record->lockState = 0;
		$record->lockUserId = NULL;
		$record->update();
		$record->save();
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['lockUserId' => 'id']);
    }

    /**
     * Return true if database is locked
     * @return boolean
     */
    public static function isLocked()
    {
    	$model = static::findOne(1);
    	return ($model->lockState == 1) ? true : false;
    }

}
