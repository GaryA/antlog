<?php
namespace app\models;

use Yii;
use yii\helpers\VarDumper;
use yii\behaviors\TimestampBehavior;
use app\models\Robot;
use app\models\Entrant;
use app\models\Fights;
use app\models\User;
use app\models\ProgressBar;

/**
 * This is the model class for table "{{%event}}".
 *
 * @property string $id
 * @property string $name
 * @property integer $eventDate
 * @property string $state
 * @property integer $classId
 * @property integer $offset
 * @property integer $eventType
 * @property integer $num_groups
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $organiserId
 * @property string $venue
 *
 * @property Entrant[] $entrants
 * @property RobotClass $class
 */
class Event extends \yii\db\ActiveRecord
{

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
			[['name', 'classId', 'eventDate', 'organiserId'], 'required'],
			[['classId', 'organiserId'], 'integer'],
			[['eventDate'], 'date', 'format' => 'yyyy-mm-dd'],
			[['state'], 'string'],
			['state', 'default', 'value' => 'Registration'],
			[['name'], 'string', 'max' => 100],
			[['venue'], 'string', 'max' => 65535],
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
	 * function to set up event and generate corresponding fights
	 *
	 * @param string $key
	 * @param integer $id
	 * @param array $teams
	 * @param integer $numEntrants
	 */
	public function setupEvent($key, $redirect, $id, $teams, $numEntrants)
	{
		$fights = new Fights();
		$entrantModel = new Entrant();
		$progress = new ProgressBar($key);

		$progress->start(6 + count($teams), $redirect);

		/* calculate required size of each group */
		$maxTeamSize = count(reset($teams));
		if ($maxTeamSize <= 2 && $numEntrants < 32)
		{
			$numGroups = 2;
		}
		else
		{
			if ($maxTeamSize <= 4 && $numEntrants < 64)
			{
				$numGroups = 4;
			}
			else
			{
				$numGroups = 8;
			}
		}
		/* assign robots to groups */
		$retVal = $this->assignGroups($teams, $numEntrants, $numGroups, $progress);
		if ($retVal[0] == 1)
		{
			/* can't fit team in remaining groups */
			$progress->stop('Team size is bigger than number of spaces available. Try re-doing the draw.');
		}
		else
		{
			$progress->inc();
			$entrants = $retVal[1];

			/* create an array of robots per group */
			$groupList = array();
			foreach ($entrants as $robot => $group)
			{
				$groupList[$group][] = $robot;
			}
			$progress->inc();
			/* add a new set of fights to the fights table */
			$fights->insertDoubleElimination($id);
			$progress->inc();
			$offset = $fights->setupEvent($id, $groupList);
			$progress->inc();
			$entrantModel->setGroups($id, $groupList);
			$progress->inc();
			/* ready to start! */
			$setupOK = $this->stateReady($id, $offset, $numGroups);
			if ($setupOK == false)
			{
				Yii::$app->getSession()->setFlash('error', 'Failed to save Ready state to event model.');
			}
			$progress->inc();
			$progress->complete();
		}
		return;
	}

	/**
	 * function to assign robots to groups
	 * return array mapping each robot to its group
	 *
	 * @param array $teams
	 * @param integer $numGroups
	 * @param integer $numEntrants
	 * @param ProgressBar $progress
	 * @return array $entrants
	 */
	private function assignGroups($teams, $numEntrants, $numGroups, $progress)
	{
		$groupSize = intval($numEntrants / $numGroups);
		$remainder = $numEntrants % $numGroups;
		/* create group arrays */
		for ($i = 1; $i <= $numGroups; $i ++)
		{
			$groupArray[$i - 1] = [
				'size' => $groupSize + (($i <= $remainder) ? 1 : 0),
				'free' => $groupSize + (($i <= $remainder) ? 1 : 0),
				'robots' => array()
			];
		}
		/* assign robots to groups - this can fail to find a solution! */
		$teamGroups = array();
		// return $this->actionDebug($id, '$groupArray', $groupArray);

		foreach ($teams as $team => $robots)
		{
			/* calculate array of groups with free slots */
			unset($temp);
			$temp = array();
			for ($i = 0; $i < $numGroups; $i ++)
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
				return [
					1,
					NULL
				];
			}
			$i = 0;
			foreach ($robots as $robot)
			{
				/* give each robot a group number */
				$groupNum = $freeGroups[$i];
				$entrants[$robot] = $groupNum;
				$groupArray[$groupNum]['free'] -= 1;
				$i ++;
			}
			$progress->inc();
		}
		return [
			0,
			$entrants
		];
	}

	/**
	 * function to set event state to "Closed"
	 */
	public function stateClosed()
	{
		$retval = false;
		$events = $this->find()
			->where(['organiserId' => Yii::$app->user->identity->id])
			->andWhere(['state' => 'Registration'])
			->all();
		foreach($events as $event)
		{
			$event->state = 'Closed';
			$event->update();
			$retval = true;
		}
		return $retval;
	}

	/**
	 * function to set event state to "Setup"
	 */
	public function stateSetup($id)
	{
		$event = $this->findOne($id);
		$event->state = 'Setup';
		return $event->update();
	}

	/**
	 * function to set event state to "Ready"
	 */
	public function stateReady($id, $offset, $numGroups)
	{
		$event = $this->findOne($id);
		$event->state = 'Ready';
		$event->offset = $offset;
		$event->num_groups = $numGroups;
		return $event->update();
	}

	/**
	 * function to set event state to "Running"
	 */
	public function stateRunning($id)
	{
		$event = $this->findOne($id);
		$event->state = 'Running';
		return $event->update();
	}

	/**
	 * function to set event state to "Registration"
	 */
	public function stateRegistration($id)
	{
		$event = $this->findOne($id);
		$event->state = 'Registration';
		return $event->update();
	}

	/**
	 * function to set event state to "Complete"
	 */
	public function stateComplete($id)
	{
		$event = $this->findOne($id);
		$event->state = 'Complete';
		return $event->update();
	}
	/**
	 * function to get teams (and their robots) for an event
	 * returns an array where the keys are team IDs and each element is
	 * an array of entrant IDs
	 */
	public static function getTeams($id)
	{
		$teams = array();
		$event = static::findOne($id);
		$entrants = $event->entrants;
		foreach ($entrants as $entrant)
		{
			$teams[$entrant->robot->teamId][] = $entrant->id;
		}
		uasort($teams, [
			'self',
			'compareSize'
		]);
		return $teams;
	}

	/**
	 * Return true if event has no entrants (so may be deleted)
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function isOKToDelete($id)
	{
		return Entrant::find()->where([
			'eventId' => $id
		])->count() > 0 ? false : true;
	}

	public static function getPosition($finalFight, $eventId)
	{
		$event = Event::findOne($eventId);
		$eventType = $event->eventType;
		$numGroups = $event->num_groups;

		switch ($eventType)
		{
			case 1:	// double elimination
				$position = static::getPosDE($finalFight);
				break;
			default:
				$position = '';
				break;
		}
		return $position;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Event',
			'eventDate' => 'Date',
			'state' => 'State',
			'classId' => 'Class ID'
		];
	}

	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getEntrants()
	{
		return $this->hasMany(Entrant::className(), [
			'eventId' => 'id'
		]);
	}

	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getClass()
	{
		return $this->hasOne(RobotClass::className(), [
			'id' => 'classId'
		]);
	}

	/**
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getOrganiser()
	{
		return $this->hasOne(User::className(), [
			'id' => 'organiserId'
		]);
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
			return ($countA > $countB) ? - 1 : 1;
		}
	}

	private static function getPosDE($finalFight)
	{
		switch ($finalFight)
		{
			case 256:
				$position = '1st';
				break;
			case 255:
			case 254:
				$position = '2nd';
				break;
			case 253:
				$position = '3rd';
				break;
			case 252:
				$position = '4th';
				break;
			case 251:
			case 250:
				$position = 'Joint 5th';
				break;
			case 249:
			case 248:
				$position = 'Joint 7th';
				break;
			case 243:
			case 244:
			case 245:
			case 246:
				$position = 'Joint 9th';
				break;
			case 237:
			case 238:
			case 239:
			case 240:
				$position = 'Joint 13th';
				break;
			default:
				$position = '';
				break;
		}
		return $position;
	}
}
