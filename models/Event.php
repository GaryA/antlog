<?php

namespace app\models;

use Yii;
use app\models\Robot;
use app\models\Entrant;

/**
 * This is the model class for table "{{%event}}".
 *
 * @property string $id
 * @property string $name
 * @property string $state
 * @property integer $classId
 * @property integer $offset
 *
 * @property Entrant[] $entrants
 * @property RobotClass $class
 */
class Event extends \yii\db\ActiveRecord
{ 
	/**
	  * function to generate an array to populate a dropdown list of all the events in the table
	  */
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
	 * function to assign robots to groups
	 * return array mapping each robot to its group
	 *
	 * @param array $teams
	 * @param integer $numGroups
	 * @param integer $numEntrants
	 * @return array $entrants
	 */
	public static function assignGroups($teams, $numEntrants, $numGroups)
	{
		$groupSize = intval($numEntrants / $numGroups);
		$remainder = $numEntrants % $numGroups;
		/* create group arrays */
		for ($i = 1; $i <= $numGroups; $i++)
		{
			$groupArray[$i - 1] = ['size' => $groupSize + (($i <= $remainder) ? 1 : 0),
									'free' => $groupSize + (($i <= $remainder) ? 1 : 0),
									'robots' => array()];
		}
		/* assign robots to groups - this can fail to find a solution! */
		$teamGroups = array();
		// return $this->actionDebug($id, '$groupArray', $groupArray);
		
		foreach ($teams as $team => $robots)
		{
			/* calculate array of groups with free slots */
			unset($temp);
			for ($i = 0; $i < $numGroups; $i++)
			{
				if ($groupArray[$i]['free'] > 0)
				{
					$temp[$i] = $groupArray[$i]['free'];
				}
			}
			$freeGroups = array_keys($temp);
			shuffle($freeGroups);
			if (count($robots) > count($freeGroups))
			{
				/* can't fit team in remaining groups */
				return [1, NULL];
			}
			$i = 0;
			foreach ($robots as $robot)
			{
				/* give each robot a group number */
				$groupNum = $freeGroups[$i];
				$entrants[$robot] = $groupNum;
				$groupArray[$groupNum]['free'] -= 1;
				$i++;
			}
		}
		return [0, $entrants];
	}

	/**
	 * function to set event state to "Setup"
	 */
	public static function stateSetup($id)
	{
		$event = static::findOne($id);
		$event->state = 'Setup';
		return($event->save(false, ['state']));
	}
	
	/**
	 * function to set event state to "Running"
	 */
	public static function stateRunning($id)
	{
		$event = static::findOne($id);
		$event->state = 'Running';
		return($event->save(false, ['state']));
	}

	/**
	 * function to get teams (and their robots) for an event
	 */
	public static function getTeams($id)
	{
		$teams = array();
		$event = static::findOne($id);
		$entrants = $event -> entrants;
		foreach($entrants as $entrant)
		{
			$teams[$entrant->robot->teamId][] = $entrant->id;
		}
		uasort($teams, ['self', 'compareSize']);
		return $teams;
	}

	/**
	 * Return true if event has no entrants (so may be deleted)
	 * @param integer $id
	 * @return boolean
	 */
	public function isOKToDelete($id)
	{
		return Entrant::find()->where(['eventId' => $id])->count() > 0 ? false : true;
	}
	
   /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
		[
            [['name', 'classId'], 'required'],
            [['classId'], 'integer'],
            [['state'], 'string'],
			['state', 'default', 'value' => 'Registration'],
			['state', 'validateState'],
            [['name'], 'string', 'max' => 20]
        ];
    }

	/**
	  * function to ensure only one event per weight class can be open at a time
	  */
	public function validateState($attribute, $params)
	{
		if (Event::find()
			->andWhere(['classId' => $this->classId])
			->andWhere(['not', ['state' => 'Complete']])
			->count() > 0)
		{
			$this->addError($attribute, 'There can be only one open event per weight class');
		}
	}
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return
		[
            'id' => 'ID',
            'name' => 'Event',
            'state' => 'State',
            'classId' => 'Class ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntrants()
    {
        return $this->hasMany(Entrant::className(), ['eventId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClass()
    {
        return $this->hasOne(RobotClass::className(), ['id' => 'classId']);
    }
	
	/**
	 * function to sort size of arrays in descending order
	 */
	private static function compareSize($a, $b)
	{
		$countA = count($a);
		$countB = count($b);
		if ($countA == $countB)
		{
			return 0;
		}
		else
		{
			return ($countA > $countB) ? -1 : 1; 
		}
	}
}
