<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%current_fight}}".
 *
 * @property integer $id
 * @property string $name
 *
 */
class CurrentFight extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%current_fight}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fightId', 'robot1', 'robot2', 'team1', 'team2', 'updated_at'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fightId' => 'Fight',
            'robot1' => 'Robot 1',
        	'robot2' => 'Robot 2',
        	'team1' => 'Team 1',
        	'team2' => 'Team 2',
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

    public function set($fightId, $title, $robot1, $robot2, $team1, $team2)
    {
    	// id is always 1
    	// set fightId = $fightId, title = $title, robot1 = $robot1, robot2 = $robot2,
    	// team1 = $team1, team2 = $team2, updated_at = now()
		$record = $this->findOne(1);
		$record->fightId = $fightId;
		$record->title = $title;
		$record->robot1 = $robot1;
		$record->robot2 = $robot2;
		$record->team1 = $team1;
		$record->team2 = $team2;
		return $record->update(false);
    }

    public function clear()
    {
    	// id is always 1
    	// set fightId = 0, title = NULL, robot1 = NULL, robot2 = NULL,
    	// team1 = NULL, team2 = NULL, updated_at = now()
		$record = $this->findOne(1);
		$record->fightId = 0;
		$record->title = NULL;
		$record->robot1 = NULL;
		$record->robot2 = NULL;
		$record->team1 = NULL;
		$record->team2 = NULL;
		return $record->update(false);
	}

    /**
     * Return ID of current fight, zero if no fight running
     * @return integer
     */
    public static function getCurrentId()
    {
    	$model = static::findOne(1);
    	return $model->fightId;
    }

}

