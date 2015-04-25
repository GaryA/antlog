<?php

namespace app\models;

use Yii;
use app\models\Robot;
use app\models\User;

/**
 * This is the model class for table "{{%team}}".
 *
 * @property string $id
 * @property string $name
 *
 * @property Entrant[] $entrants
 * @property Robot[] $robots
 */
class Team extends \yii\db\ActiveRecord
{
	public static function dropdown()
	{
		$models = static::find()->all();
		foreach ($models as $model)
		{
			$dropdown[$model->id] = $model->name;
		}
		return $dropdown;
	}
	
	/**
	 * Return true if team contains no robots (so may be deleted)
	 * @param integer $id
	 * @return boolean
	 */
	public function isEmpty($id)
	{
		return Robot::find()->where(['teamId' => $id])->count() > 0 ? false : true;
	}

	/**
	 * Return true if team belongs to logged-in user
	 * @param model $model
	 * @return boolean
	 */
	public function isUser($model)
	{
		if (!Yii::$app->user->isGuest)
		{
			return (Yii::$app->user->identity->id == $model->userId) ? true : false;
		}
		else
		{
			return false;
		}
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
			[['name'], 'unique', 'message' => 'Team "{value}" has already been created.'],
            [['name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Team',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobots()
    {
        return $this->hasMany(Robot::className(), ['teamId' => 'id']);
    }
}
