<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Event;
use app\models\Entrant;

/**
 * This is the model class for table "{{%fights}}".
 *
 * @property string $id
 * @property integer $eventId
 * @property integer $fightGroup
 * @property integer $fightRound
 * @property string $fightBracket
 * @property integer $fightNo
 * @property integer $robot1Id
 * @property integer $robot2Id
 * @property integer $winnerId
 * @property integer $loserId
 * @property string $winnerNextFight
 * @property string $loserNextFight
 * @property integer $sequence
 *
 * @property Robot $robot1
 * @property Robot $robot2
 * @property Robot $winner
 * @property Robot $loser
 */
class Fights extends ActiveRecord
{
	protected $_startMap = [
		[1, 9, 17, 25, 33, 41, 49, 57, 'robot1Id'],
		[2, 10, 18, 26, 34, 42, 50, 58, 'robot1Id'],
		[3, 11, 19, 27, 35, 43, 51, 59, 'robot1Id'],
		[4, 12, 20, 28, 36, 44, 52, 60, 'robot1Id'],
		[5, 13, 21, 29, 37, 45, 53, 61, 'robot1Id'],
		[6, 14, 22, 30, 38, 46, 54, 62, 'robot1Id'],
		[7, 15, 23, 31, 39, 47, 55, 63, 'robot1Id'],
		[8, 16, 24, 32, 40, 48, 56, 64, 'robot1Id'],
		[1, 9, 17, 25, 33, 41, 49, 57, 'robot2Id'],
		[2, 10, 18, 26, 34, 42, 50, 58, 'robot2Id'],
		[3, 11, 19, 27, 35, 43, 51, 59, 'robot2Id'],
		[4, 12, 20, 28, 36, 44, 52, 60, 'robot2Id'],
		[5, 13, 21, 29, 37, 45, 53, 61, 'robot2Id'],
		[6, 14, 22, 30, 38, 46, 54, 62, 'robot2Id'],
		[7, 15, 23, 31, 39, 47, 55, 63, 'robot2Id'],
		[8, 16, 24, 32, 40, 48, 56, 64, 'robot2Id'],
	];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%fights}}';
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
        return [
            [['eventId', 'fightGroup', 'fightRound', 'fightBracket', 'fightNo', 'winnerNextFight', 'loserNextFight'], 'required'],
            [['eventId', 'fightGroup', 'fightRound', 'fightNo', 'robot1Id', 'robot2Id', 'winnerId', 'loserId', 'winnerNextFight', 'loserNextFight', 'sequence'], 'integer'],
            [['fightBracket'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
			'eventId' => 'Event ID',
            'fightGroup' => 'Fight Group',
            'fightRound' => 'Fight Round',
            'fightBracket' => 'Fight Bracket',
            'fightNo' => 'Fight No',
            'robot1Id' => 'Robot1 ID',
            'robot2Id' => 'Robot2 ID',
            'winnerId' => 'Winner ID',
            'loserId' => 'Loser ID',
            'winnerNextFight' => 'Winner Next Fight',
            'loserNextFight' => 'Loser Next Fight',
            'sequence' => 'Sequence',
        ];
    }

    /**
     * Check whether it is OK to change the result of a fight
     * @param string $id
     * @return boolean
     */
    public function isOKToChange($id)
    {
    	$record = $this->findOne($id);
    	$winnerNext = $record->winnerNextFight + $id;
    	$loserNext = $record->loserNextFight + $id;
    	$retVal = $this->checkRecord($winnerNext);
    	if (strpos($retVal, 'OK') === 0)
    	{
    		if ($record->loserNextFight != 0)
    		{
    			$retVal = $this->checkRecord($loserNext);
    		}
    	}
   		return $retVal . ' ' . $winnerNext . ' ' . $loserNext;
	}

    public function checkRecord($id)
    {
    	$record = $this->findOne($id);
    	if ($record->winnerId == -1)
    	{
    		// next fight is OK
    		return 'OK ' . $id . ' ' . $record->winnerId . ' ' . $record->loserId;
    	}
    	else if ($record->loserId == 0)
    	{
    		// next fight is a bye so go on to the next fight

    		/*
    		 * This needs to be a recursive function to traverse an arbitrary number of byes (for small events!)
    		 * But for some reason a 500 error is returned...
    		 * Try running on antlog.localso that debug is turned on and maybe some more useful error message
    		 * will be forthcoming...
    		 */

    		return $this->checkRecord($record->winnerNextFight + $id);
    		//$id += $record->winnerNextFight;
    		//$record = $this->findOne($id);
    		//if ($record->winnerId == -1)return 'OK ' . $id . ' ' . $record->winnerId . ' ' . $record->loserId;
    		//return 'Bad ' . $id . ' ' . $record->winnerId . ' ' . $record->loserId;
    	}
    	else
    	{
    		// next fight is not OK so can't change result
    		return 'Bad ' . $id . ' ' . $record->winnerId . ' ' . $record->loserId;
    	}
    }

    public function updateCurrent($id, $winner)
    {
    	$record = $this->findOne($id);
    	if ($winner == $record->robot1->id)
    	{
    		$loser = $record->robot2->id;
    	}
    	else if ($winner == $record->robot2->id)
    	{
    		$loser = $record->robot1->id;
    	}
    	else
    	{
    		$error = "Winner = $winner but does not match Robot1 $record->robot1->id or Robot2 $record->robot2->id";
    		return ['debug', 'id' => $id, 'name' => 'Error', 'value' => $error];
    	}

    	$record->winnerId = $winner;
    	$record->loserId = $loser;
    	// Calculate and insert sequence number
		$sequence = $this->find()
		   ->where(['eventId' => $record->eventId])
		   ->andWhere(['>=', 'sequence', 0])
		   ->count();
    	$record->sequence = $sequence;
    	$record->update();

    	$fightLoser = Entrant::findOne($loser);

    	$finished = true;
    	if ($record->winnerNextFight > 0)
    	{
    		if ($record->fightBracket == 'F')
    		{
    			if ($fightLoser->status == 1)
    			{
    				/* first final fight, no need for a rematch, make the second final a bye */
    				$this->updateNext($record->id, $record->winnerNextFight, $record->winnerId);
    				$this->updateNext($record->id, $record->loserNextFight, 0);
    			}
    			else
    			{
    				/* first final fight but need a rematch */
    				$finished = false;
    				$this->updateNext($record->id, $record->winnerNextFight, $record->winnerId);
    				$this->updateNext($record->id, $record->loserNextFight, $record->loserId);
    			}
    		}
    		else
    		{
    			$finished = false;
    			$this->updateNext($record->id, $record->winnerNextFight, $record->winnerId);
    			$this->updateNext($record->id, $record->loserNextFight, $record->loserId);
    		}
    		do
    		{
    			$status = $this->runByes($record->eventId);
    		} while ($status == true);
    	}
    	if ($record->save())
    	{
    		$event = Event::findOne($record->eventId);
    		$fightLoser->status -= 1;
    		if ($fightLoser->status == 0)
    		{
    			$fightLoser->finalFightId = $record->id - $event->offset;
    		}
    		$fightLoser->touch('updated_at');
    		$fightLoser->save(false, ['status', 'finalFightId']);
    		if ($finished)
    		{
    			$entrant = Entrant::findOne($winner);
    			$entrant->finalFightId = 256;
    			$entrant->touch('updated_at');
    			$entrant->save(false, ['finalFightId']);
    			/* update event state */
    			$event->state = 'Complete';
    			$event->update();
    			//$event->save(false, ['state']);
    			/* announce results! */
    			return ['event/result', 'id' => $record->eventId];
    		}
    		else
    		{
    			return ['index', 'eventId' => $record->eventId, 'byes' => 0, 'complete' => 0];
    		}
    	}
    	else
    	{
    		$error = "Failed to save model to database";
    		return ['debug', 'id' => $id, 'name' => 'Error', 'value' => $error];
    	}
    }
	/**
	 * function to run byes
	 * @param integer $id
	 * @return boolean true if a bye was found and run
	 */
	public function runByes($id)
	{
		$status = false;
		$record = $this->find()
			->where(['eventId' => $id, 'robot1Id' => 0, 'winnerId' => -1])
			->andWhere(['>=', 'robot2Id', 0])
			->orderBy('id')
			->one();
		if ($record != NULL)
		{
			$winner = $record->robot2Id;
			$loser = $record->robot1Id;
		}
		else
		{
			$record = $this->find()
				->where(['eventId' => $id, 'robot2Id' => 0, 'winnerId' => -1])
				->andWhere(['>=', 'robot1Id', 0])
				->orderBy('id')
				->one();
			if ($record != NULL)
			{
				$winner = $record->robot1Id;
				$loser = $record->robot2Id;
			}
		}
		if ($record != NULL)
		{
			$record->winnerId = $winner;
			$record->loserId = $loser;
			// Calculate and insert sequence number
			$sequence = $this->find()
			   ->where(['eventId' => $id])
		   		->andWhere(['>=', 'sequence', 0])
			   ->count();
			$record->sequence = $sequence;
			$record->update();
			$status = $this->updateNext($record->id, $record->winnerNextFight, $record->winnerId);
			if ($status == true)
			{
				$status = $this->updateNext($record->id, $record->loserNextFight, $record->loserId);
			}
		}
		return $status;
	}

	/**
	 * update record for winner's or loser's next fight
	 */
	public function updateNext($id, $nextFight, $robotId)
	{
		$status = false;
		if ($nextFight != 0)
		{
			$nextRecord = $this->findOne($id + $nextFight);

			if ($nextRecord != NULL)
			{
				if ($nextRecord->robot1Id == -1)
				{
					$nextRecord->robot1Id = $robotId;
					$nextRecord->update();
					$status = true;
				}
				else if (($nextRecord->robot2Id == -1) && (($nextRecord->robot1Id != $robotId) || ($nextRecord->robot1Id == 0)))
				{
					$nextRecord->robot2Id = $robotId;
					$nextRecord->update();
					$status = true;
				}
			}
		}
		else
		{
			/* there is no next fight so just skip and return true keep checking for fights */
			$status = true;
		}
		return $status;
	}

	/**
	 * function to put robot ids into fights table at start of event
	 * @param integer $id
	 * @param array $groupList
	 * @return integer $offset offset into fights table for start of current event
	 */
	public function setupEvent($id, $groupList)
	{
		/* get offset of first fight for this event */
		$record = $this->find()
			->where(['eventId' => $id])
			->orderBy('id')
			->one();
		$offset = $record->id - 1;
		$sequence = 0;
		foreach ($groupList as $groupNum => $group)
		{
			/* shuffle robots within groups */
			shuffle($group);
			/* put robot ids into fights table */
			foreach ($group as $index => $robot)
			{
				$fightId = $this->_startMap[$index][$groupNum] + $offset;
				$column = $this->_startMap[$index][8];
				Yii::$app->db->createCommand("UPDATE {{%fights}}
				   SET `$column` = $robot, `sequence` = $sequence
				   WHERE `id` = $fightId")
				   ->execute();
				$sequence++;
			}
		}
		return $offset;
	}
	/**
	 * inserts a set of double elimination fights into the table
	 * @return integer (the offset into the fights table where this event starts)
	 */
	public function insertDoubleElimination($eventId)
	{
		Yii::$app->db->createCommand('INSERT INTO {{%fights}} (`fightGroup`,`fightRound`,`fightBracket`,
			`fightNo`,`robot1Id`,`robot2Id`,`winnerId`,`loserId`,`winnerNextFight`,`loserNextFight`)
			SELECT `fightGroup`,`fightRound`,`fightBracket`,`fightNo`,`robot1Id`,`robot2Id`,`winnerId`,
			`loserId`,`winnerNextFight`,`loserNextFight`
			FROM {{%double_elim}}
			WHERE `winnerId` = -1 ORDER BY `id`')
			->execute();
		Yii::$app->db->createCommand('UPDATE {{%fights}}
			SET `eventId` = ' . $eventId .
			' WHERE `winnerId` = -1')
			->execute();
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot1()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'robot1Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot2()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'robot2Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWinner()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'winnerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoser()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'loserId']);
    }
}
