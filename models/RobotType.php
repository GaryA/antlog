<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%robot_type}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Robot[] $robots
 */
class RobotType extends \yii\db\ActiveRecord
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%robot_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobots()
    {
        return $this->hasMany(Robot::className(), ['typeId' => 'id']);
    }
}
