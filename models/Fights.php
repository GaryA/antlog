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
    		return $this->checkRecord($record->winnerNextFight + $id);
    	}
    	else
    	{
    		// next fight is not OK so can't change result
    		return 'Bad ' . $id . ' ' . $record->winnerId . ' ' . $record->loserId;
    	}
    }

    /**
     * function to update the current fight, then call further functions to
     * update subsequent fights and run byes
     * @param integer $id
     * @param integer $winner
     * @param integer $showComplete
     * @param string $change - optional, if true change existing result
     * @return multitype:string unknown |multitype:string NULL |multitype:string number NULL
     */
    public function updateCurrent($id, $winner, $showComplete, $change = false, $replacement = 0)
    {
    	$record = $this->findOne($id);
    	if ($change === true)
    	{
    		$loser = $winner;
    		$winner = $replacement;
    	}
    	else
    	{
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
    			return ['/site/debug', 'class' => __CLASS__, 'function' => __FUNCTION__, 'name' => 'ERROR', 'value' => $error];
    		}
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
    	$fightWinner = Entrant::findOne($winner);
    	$error = false;
    	if ($record->fightBracket == 'W' && $change == false)
    	{
    		if ($fightWinner->status != 2)
    		{
    			Yii::info("ID: $fightWinner->id, Bracket = Winners but Status = $fightWinner->status", __METHOD__);
    			$fightWinner->status = 2;
    			$fightWinner->save(false, ['status']);
    			$error = true;
    		}
    		if ($fightLoser->status != 2)
    		{
    			Yii::info("ID: $fightLoser->id, Bracket = Winners but Status = $fightLoser->status", __METHOD__);
    			$fightLoser->status = 2;
    			$fightLoser->save(false, ['status']);
     			$error = true;
    		}
    	}
		else if ($record->fightBracket == 'L' && $change == false)
		{
			if ($fightWinner->status != 1)
			{
    			Yii::info("ID: $fightWinner->id, Bracket = Losers but Status = $fightWinner->status", __METHOD__);
				$fightWinner->status = 1;
				$fightWinner->save(false, ['status']);
				$error = true;
			}
			if ($fightLoser->status != 1)
			{
    			Yii::info("ID: $fightLoser->id, Bracket = Losers but Status = $fightLoser->status", __METHOD__);
				$fightLoser->status = 1;
				$fightLoser->save(false, ['status']);
				$error = true;
			}
		}

    	$finished = true;
    	if ($record->winnerNextFight > 0)
    	{
    		if (($record->fightBracket == 'F') && ($fightLoser->status == 1))
    		{
    			/* first final fight, no need for a rematch, make the second final a bye */
    			$this->updateNext($record->id, $record->winnerNextFight, $record->winnerId, $change, 0);
    			$this->updateNext($record->id, $record->loserNextFight, 0, $change, $record->loserId);
    		}
    		else
    		{
    			$finished = false;
    			$this->updateNext($record->id, $record->winnerNextFight, $record->winnerId, $change, $record->loserId);
    			$this->updateNext($record->id, $record->loserNextFight, $record->loserId, $change, $record->winnerId);
    		}
     		if ($change === false)
    		{
    			do
    			{
    				$status = $this->runByes($record->eventId);
    			} while ($status == true);
    		}
    		else
    		{
    			$id = $record->id;
    			do
    			{
    				$id = $this->changeByes($record->eventId, $record->winnerId, $record->loserId, $id);
    			} while ($id > 0);
       		}
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
    		if ($change === true)
    		{
    			$fightWinner = Entrant::findOne($winner);
    			$fightWinner->status += 1;
    			$fightWinner->finalFightId = 0;
    			$fightWinner->touch('updated_at');
    			$fightWinner->save(false, ['status', 'finalFightId']);
    		}
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
    			if ($error)
    			{
    				Yii::$app->session->setFlash('error', 'Something went wrong with the robot tracking. It should be fixed now. Please send the fights.log file with the database updates.');
    			}
    			return ['index', 'eventId' => $record->eventId, 'byes' => 1, 'complete' => $showComplete];
    		}
    	}
    	else
    	{
    		$error = "Failed to save model to database";
    		return ['/site/debug', 'class' => __CLASS__, 'function' => __FUNCTION__, 'name' => 'ERROR', 'value' => $error];
    	}
    }
	/**
	 * function to run byes
	 * @param integer $eventId
	 * @return boolean true if a bye was found and run
	 */
	public function runByes($eventId)
	{
		$status = false;
		$record = $this->find()
			->where(['eventId' => $eventId, 'robot1Id' => 0, 'winnerId' => -1])
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
				->where(['eventId' => $eventId, 'robot2Id' => 0, 'winnerId' => -1])
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
			   ->where(['eventId' => $eventId])
	   			->andWhere(['>=', 'sequence', 0])
			   ->count();
			$record->sequence = $sequence++;
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
	 * function to change byes
	 * @param $eventId integer
	 * @param $winnerId integer
	 * @param $loserId integer
	 * @param $fightId integer
	 */
	public function changeByes($eventId, $winnerId, $loserId, $fightId)
	{
		// function needs to find fights where id > $record->id
		// and ((robot1Id = 0 and robot2Id is in [winner_Id, loser_Id])
		// or (robot2Id = 0 and robot1Id is in [winner_Id, loser_Id]))
		// The and/or logic needs to be organised correctly...
		$record = $this->find()
			->where(['eventId' => $eventId])
			->andWhere(['>', 'id', $fightId])
			->andWhere(['or',
				['robot1Id' => 0, 'robot2Id' => [$winnerId, $loserId]],
				['robot2Id' => 0, 'robot1Id' => [$winnerId, $loserId]]])
			->orderBy('id')
			->one();
		if ($record != NULL)
		{
			if (($record->robot1Id == $winnerId) || ($record->robot2Id == $winnerId))
			{
				$record->winnerId = $winnerId;
				$replacement = $winnerId;
				$original = $loserId;
			}
			else if (($record->robot1Id == $loserId) || ($record->robot2Id == $loserId))
			{
				$record->winnerId = $loserId;
				$replacement = $loserId;
				$original = $winnerId;
			}
			$record->update();
			$status = $this->updateNext($record->id, $record->winnerNextFight, $replacement, true, $original);
			if ($status == true)
			{
				$status = $this->updateNext($record->id, $record->loserNextFight, $replacement, true, $original);
			}
			return $record->id;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * update record for winner's or loser's next fight
	 */
	public function updateNext($id, $nextFight, $robotId, $change = false, $original = 0)
	{
		$status = false;
		if ($nextFight != 0)
		{
			$nextRecord = $this->findOne($id + $nextFight);
			if ($nextRecord != NULL)
			{
				if ($change === false)
				{
					if ($nextRecord->robot1Id == -1)
					{
						$nextRecord->robot1Id = $robotId;
					}
					else if (($nextRecord->robot2Id == -1) && (($nextRecord->robot1Id != $robotId) || ($nextRecord->robot1Id == 0)))
					{
						$nextRecord->robot2Id = $robotId;
					}
				}
				else
				{
					if ($nextRecord->robot1Id == $original)
					{
						$nextRecord->robot1Id = $robotId;
					}
					else if ($nextRecord->robot2Id == $original)
					{
						$nextRecord->robot2Id = $robotId;
					}
				}
				$nextRecord->update();
				$status = true;
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
			foreach ($group as $index => $robotId)
			{
				$fightId = $this->_startMap[$index][$groupNum] + $offset;
				$column = $this->_startMap[$index][8];
				$command = Yii::$app->db->createCommand("UPDATE {{%fights}}
				   SET `$column` = $robotId, `sequence` = $sequence
				   WHERE `id` = $fightId");
				$command->execute();
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
		$createTime = time();
		$command = Yii::$app->db->createCommand("INSERT INTO {{%fights}} (`eventId`,`fightGroup`,`fightRound`,`fightBracket`,
			`fightNo`,`robot1Id`,`robot2Id`,`winnerId`,`loserId`,`winnerNextFight`,`loserNextFight`, `created_at`, `updated_at`)
			SELECT $eventId,`fightGroup`,`fightRound`,`fightBracket`,`fightNo`,`robot1Id`,`robot2Id`,`winnerId`,
			`loserId`,`winnerNextFight`,`loserNextFight`, $createTime, $createTime
			FROM {{%double_elim}}
			WHERE `winnerId` = -1 ORDER BY `id`");
		$command->execute();
	}

	/**
	 * Create text label from round, group and bracket
	 * @param ActiveRecord $model
	 * @return string
	 */
	public static function labelRound($model)
    {
    	if ($model->fightRound == 15)
    	{
    		$retVal = "Final (replay)";
		}
    	else if ($model->fightRound == 14)
    	{
    		$retVal = "Final";
		}
    	else if ($model->fightRound == 13)
    	{
    		$retVal = "Third Place Play-off";
		}
    	else if ($model->fightGroup == 9)
    	{
    		$retVal = "Finals Round $model->fightRound, $model->fightBracket bracket";
    	}
    	else
    	{
    		if ($model->fightBracket == 'W')
    		{
    			$bracket = "Winners' bracket";
    		}
    		else
    		{
    			$bracket = "Losers' bracket";
    		}
    		$retVal = "Group $model->fightGroup Round $model->fightRound, $bracket";
    	}
    	return $retVal;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot1()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'robot1Id'])->from(['robot1' => Entrant::tableName()]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot2()
    {
        return $this->hasOne(Entrant::className(), ['id' => 'robot2Id'])->from(['robot2' => Entrant::tableName()]);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getNextFights($id)
    {
    	return self::Find()->innerJoin('{{%event}} e', 'e.id = eventId')
    	->leftJoin('{{%entrant}} n1', 'n1.id = robot1Id')
    	->leftJoin('{{%entrant}} n2', 'n2.id = robot2Id')
    	->leftJoin('{{%robot}} r1', 'r1.id = n1.robotId')
    	->leftJoin('{{%robot}} r2', 'r2.id = n2.robotId')
    	->leftJoin('{{%user}} u1', 'u1.id = r1.teamId')
    	->leftJoin('{{%user}} u2', 'u2.id = r2.teamId')
    	->where(['winnerId' => -1])
    	->andWhere(['like', 'e.state', 'Running'])
    	->andWhere(['or', "r1.teamId = $id", "r2.teamId = $id"]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function getCompleteFights($event, $robot)
    {
    	return self::Find()->innerJoin('{{%event}} e', 'e.id = eventId')
    	->leftJoin('{{%entrant}} n1', 'n1.id = robot1Id')
    	->leftJoin('{{%entrant}} n2', 'n2.id = robot2Id')
    	->leftJoin('{{%robot}} r1', 'r1.id = n1.robotId')
    	->leftJoin('{{%robot}} r2', 'r2.id = n2.robotId')
    	->leftJoin('{{%user}} u1', 'u1.id = r1.teamId')
    	->leftJoin('{{%user}} u2', 'u2.id = r2.teamId')
    	->where(['e.id' => $event])
    	->andWhere(['>', 'winnerId', 0])
    	->andWhere(['and', "robot1Id > 0", "robot2Id > 0"])
    	->andWhere(['or', "n1.robotId = $robot", "n2.robotId = $robot"]);
    }
}
