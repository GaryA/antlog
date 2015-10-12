<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Entrant;
use app\models\Event;
use app\models\Robot;

/**
 * EntrantSearch represents the model behind the search form about `app\models\Entrant`.
 */
class EntrantSearch extends Entrant
{
	public $teamName;

	public function attributes()
	{
		return array_merge(parent::attributes(), ['event.name', 'robot.team.team_name', 'robot.name']);
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'eventId', 'robotId'], 'integer'],
            [['event.name', 'teamName', 'robot.name', 'status'], 'safe'],
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
        $query = Entrant::find();
        $query->joinWith(['event', 'robot', 'user']);
        if ($params['eventId'] !== NULL)
        {
        	$query->andFilterWhere([
        		'eventId' => $params['eventId'],
        	]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->sort->attributes['teamName'] =
		[
			'asc' => ['{{%user}}.team_name' => SORT_ASC],
			'desc' => ['{{%user}}.team_name' => SORT_DESC],
		];

		$dataProvider->sort->attributes['robot.name'] =
		[
			'asc' => ['{{%robot}}.name' => SORT_ASC],
			'desc' => ['{{%robot}}.name' => SORT_DESC],
		];

		$dataProvider->sort->attributes['status'] =
		[
			'asc' => ['{{%entrant}}.status' => SORT_ASC],
			'desc' => ['{{%entrant}}.status' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
			'robotId' => $this->robotId,
        ]);

		$query->andFilterWhere(['{{%user}}.id' => $this->teamName]);
		$query->andFilterWhere(['like', '{{%robot}}.name', $this->getAttribute('robot.name')]);
		$query->andFilterWhere(['like', '{{%entrant}}.status', $this->status]);

        return $dataProvider;
    }

}