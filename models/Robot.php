<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\Event;
use app\models\Entrant;
use app\models\Fights;

/**
 * This is the model class for table "{{%robot}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $teamId
 * @property integer $classId
 * @property string $type
 * @property integer $active
 *
 * @property DoubleElim[] $doubleElims
 * @property Entrant[] $entrants
 * @property User $team
 * @property RobotClass $class
 */
class Robot extends \yii\db\ActiveRecord
{
	/**
	 * Generate array to populate dropdown list in forms
	 * @param boolean $active
	 * @return array
	 */
	public static function dropdown($active = NULL, $event = NULL)
	{
		$query = static::find();
		if (isset($active))
		{
			$query->andWhere(['active' => $active]);
		}
		if (isset($event))
		{
			// get all ids of robots entered in current event, return as array
			$array = ArrayHelper::getColumn(Entrant::find()->where(['eventId' => $event])->all(), 'robotId');
			// modify query to exclude ids in list
			$query->andWhere(['not in', 'id', $array]);
		}
		$models = $query->all();
		foreach ($models as $model)
		{
			if ($model->type != '')
			{
				$dropdown[$model->id] = $model->name . ' (' . $model->type . ')';
			}
			else
			{
				$dropdown[$model->id] = $model->name;
			}
		}
		return $dropdown;
	}

	/**
	 * Return checked if robot is entrant to any event which is not complete
	 * Return value is checked attribute of checkbox
	 * @param integer $target
	 * @return string
	 */
	public static function isSignedUp($target)
	{
		if ($event = Event::find()->andWhere(['not',['state' => 'Complete']])->one())
		{
			$robots = static::find()
				->joinWith('entrants')
				->andWhere(['{{%entrant}}.`robotId`' => $target])
				->andWhere(['{{%entrant}}.`eventId`' => $event->id])
				->count();
			return ($robots > 0) ? 'checked' : '';
		}
		return '';
	}

	/**
	 * Return true if robot is not in any event (so may be deleted)
	 * @param integer $id
	 * @return boolean
	 */
	public function isOKToDelete($id)
	{
		return Entrant::find()->where(['robotId' => $id])->count() > 0 ? false : true;
	}

	/**
	 * Return true if robot is not in any fight (so may be edited)
	 * @param integer $id
	 * @return boolean
	 */
	public function isOKToEdit($id)
	{
		$entrants = Entrant::find()->where(['robotId' => $id])->all();
		foreach ($entrants as $entrant)
		{
			if (Fights::find()->where(['robot1Id' => $entrant->id])
				->orWhere(['robot2Id' => $entrant->id])
				->count() > 0)
			{
				return false;
			}
		}
		return true;
	}

	public function isOKToRetire($id)
	{
		$entrants = Entrant::find()->where(['robotId' => $id])->all();
		foreach ($entrants as $entrant)
		{
			if (Event::find()->where(['id' => $entrant->eventId])
				->andWhere(['not', ['state' => 'Complete']]))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Return true if robot belongs to logged-in user
	 * @param model $model
	 * @return boolean
	 */
	public function isUser($model)
	{
		if (!Yii::$app->user->isGuest)
		{
			return (Yii::$app->user->identity->id == $model->teamId) ? true : false;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Return string representation of boolean 'active' flag
	 * @param record $data
	 * @return string
	 */
	public function getActive($data)
	{
		return $data->active ? 'Yes' : 'No';
	}
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%robot}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
		[
            [['name', 'teamId', 'classId', 'active'], 'required'],
            [['teamId', 'classId'], 'integer'],
			[['active'],'boolean'],
			[['active'], 'default', 'value' => 1],
            [['name'], 'string', 'max' => 50],
			[['name'], 'unique', 'message' => 'Robot name "{value}" is already taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return
		[
            'id' => 'ID',
            'name' => 'Robot Name',
            'teamId' => 'Team Name',
            'classId' => 'Class',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoubleElims()
    {
        return $this->hasMany(DoubleElim::className(), ['loserId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntrants()
    {
        return $this->hasMany(Entrant::className(), ['robotId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(User::className(), ['id' => 'teamId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(RobotClass::className(), ['id' => 'classId']);
    }
}
