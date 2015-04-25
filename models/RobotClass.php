<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%robot_class}}".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Robot[] $robots
 */
class RobotClass extends \yii\db\ActiveRecord
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
        return '{{%robot_class}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Class',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRobots()
    {
        return $this->hasMany(Robot::className(), ['classId' => 'id']);
    }
}
