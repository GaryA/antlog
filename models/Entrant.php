<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%entrant}}".
 *
 * @property string $id
 * @property string $eventId
 * @property string $robotId
 * @property string $teamId
 * @property integer $teamSize
 * @property integer $status
 * @property integer $group
 *
 * @property Event $event
 * @property Robot $robot
 * @property Team $team
 */
class Entrant extends ActiveRecord
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

	public function beforeSave($insert)
	{
		if ($insert)
		{
			$this->status = 2;
		}
		return parent::beforeSave($insert);
	}

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%entrant}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eventId', 'robotId'], 'required'],
            [['eventId', 'robotId', 'status'], 'integer'],
        	['robotId', 'validateRobot'],
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
            'robotId' => 'Robot ID',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['id' => 'eventId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot()
    {
        return $this->hasOne(Robot::className(), ['id' => 'robotId']);
    }

	/**
	 * Return true if entrant may be edited/deleted (event is in Registration state)
	 * @param integer $id
	 * @return boolean
	 */
	public function isEditable($id)
	{
		$model = Event::findOne($id);
		return ($model->state == 'Registration') ? true : false;
	}

	/**
	 * Set group numbers of entrants for current event
	 * @param integer $id The id of the current event
	 * @param array $groupList Array of groups, each element is an array of entrant IDs
	 */
	public function setGroups($id, $groupList)
	{
		Yii::trace('Entering ' . __METHOD__);
		foreach ($groupList as $group => $entrants)
		{
			$group_num = $group + 1;
			foreach ($entrants as $entrantId)
			{
				Yii::$app->db->createCommand("UPDATE {{%entrant}}
					SET `group_num` = $group_num
					WHERE `id` = $entrantId")
					->execute();
			}
		}
		Yii::trace('Leaving ' . __METHOD__);
	}

	/**
	 * function to ensure a robot can only enter an event once
	 */
	public function validateRobot($attribute, $params)
	{
		if (Entrant::find()->andWhere(['eventId' => $this->eventId])
			->andWhere(['robotId' => $this->robotId])
			->count() > 0)
		{
			$this->addError($attribute, 'The selected robot is aready entered into the current event');
		}
	}

}
