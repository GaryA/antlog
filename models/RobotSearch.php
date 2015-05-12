<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Robot;

/**
 * RobotSearch represents the model behind the search form about `app\models\Robot`.
 */
class RobotSearch extends Robot
{
	public function attributes()
	{
		return array_merge(parent::attributes(), ['team.name', 'class.name']);
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'teamId', 'classId'], 'integer'],
            [['name', 'team.name', 'class.name', 'type'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Robot::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['team.name'] = [
			'asc' => ['{{%user}}.team_name' => SORT_ASC],
			'desc' => ['{{%user}}.team_name' => SORT_DESC],
		];

		$dataProvider->sort->attributes['class.name'] = [
			'asc' => ['{{%robot_class}}.name' => SORT_ASC],
			'desc' => ['{{%robot_class}}.name' => SORT_DESC],
		];

		$query->joinWith(['team', 'class']);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'teamId' => $this->teamId,
            'classId' => $this->classId,
        ]);

        $query->andFilterWhere(['like', '{{%robot}}.name', $this->name]);
        $query->andFilterWhere(['like', '{{%robot}}.type', $this->type]);

		$query->andFilterWhere(['like', '{{%user}}.team_name', $this->getAttribute('team.name')]);
		$query->andFilterWhere(['like', '{{%robot_class}}.name', $this->getAttribute('class.name')]);

        return $dataProvider;
    }

}