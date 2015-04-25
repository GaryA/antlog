<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%double_elim}}".
 *
 * @property string $id
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
class DoubleElim extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%double_elim}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fightGroup', 'fightRound', 'fightBracket', 'fightNo', 'winnerNextFight', 'loserNextFight'], 'required'],
            [['fightGroup', 'fightRound', 'fightNo', 'robot1Id', 'robot2Id', 'winnerId', 'loserId', 'winnerNextFight', 'loserNextFight', 'sequence'], 'integer'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getRobot1()
    {
        return $this->hasOne(Robot::className(), ['id' => 'robot1Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobot2()
    {
        return $this->hasOne(Robot::className(), ['id' => 'robot2Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWinner()
    {
        return $this->hasOne(Robot::className(), ['id' => 'winnerId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoser()
    {
        return $this->hasOne(Robot::className(), ['id' => 'loserId']);
    }
}
