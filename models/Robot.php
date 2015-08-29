<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
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
 * @property integer $typeId
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
	 * Generate array to populate dropdown list in forms
	 * @param boolean $active
	 * @param integer $eventId
	 * @param integer $teamId
	 * @return array
	 */
	public static function dropdown($active = NULL, $eventId = NULL, $teamId = NULL)
	{
		$query = static::find();
		if (isset($active))
		{
			$query->andWhere(['active' => $active]);
		}
		if (isset($teamId))
		{
			$query->andWhere(['teamId' => $teamId]);
		}
		if (isset($eventId))
		{
			$event = Event::findOne($eventId);
			// get all ids of robots entered in current event, return as array
			$array = ArrayHelper::getColumn(Entrant::find()->where(['eventId' => $eventId])->all(), 'robotId');
			// modify query to exclude ids in list
			$query->andWhere(['not in', 'id', $array]);
			// modify query to exclude robots from bigger classes than the event is for
			$query->andWhere(['<=', 'classId', $event->classId]);
		}
		$models = $query->all();
		$dropdown = [];
		foreach ($models as $model)
		{
			if ($model->typeId != 0)
			{
				$dropdown[$model->id] = $model->name . ' (' . $model->type->name . ')';
			}
			else
			{
				$dropdown[$model->id] = $model->name;
			}
		}
		return $dropdown;
	}

	/**
	 * Return checked if robot is entrant to any event which is not complete (or selected event)
	 * Return value is checked attribute of checkbox
	 * @param integer $target
	 * @param integer $eventId (optional)
	 * @return string
	 */
	public static function isSignedUp($target, $eventId = NULL)
	{
		if ($eventId == NULL)
		{
			if ($events = Event::find()->andWhere(['not',['state' => 'Complete']])->all())
			{
				$robots = 0;
				foreach ($events as $event)
				{
					$robots += static::find()
						->joinWith('entrants')
						->andWhere(['{{%entrant}}.`robotId`' => $target])
						->andWhere(['{{%entrant}}.`eventId`' => $event->id])
						->count();
				}
				return ($robots > 0) ? 'checked' : '';
			}
			return '';
		}
		else
		{
			$robots = static::find()
			->joinWith('entrants')
			->andWhere(['{{%entrant}}.`robotId`' => $target])
			->andWhere(['{{%entrant}}.`eventId`' => $eventId])
			->count();
			return ($robots > 0) ? 'checked' : '';
		}
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
				->andWhere(['not', ['state' => 'Complete']])->count() > 0)
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
            [['name', 'teamId', 'classId', 'typeId', 'active'], 'required'],
            [['teamId', 'classId', 'typeId'], 'integer'],
			[['active'],'boolean'],
			[['active'], 'default', 'value' => 1],
            [['name'], 'string', 'max' => 100],
			[['name'], 'unique',
				'targetAttribute' => ['name', 'teamId'],
				'message' => 'Team already contains a robot named "{value}".'],
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
			'typeId' => 'Type',
			'active' => 'Active',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
    	return $this->hasOne(RobotType::className(), ['id' => 'typeId']);
    }
}
