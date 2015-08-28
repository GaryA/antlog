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
		return array_merge(parent::attributes(), ['event.name', 'robot.team.name', 'robot.name']);
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
        $query->joinWith(['event', 'robot']);
        if ($params['eventId'] !== NULL)
        {
        	$query->andFilterWhere([
        		'eventId' => $params['eventId'],
        	]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

		$dataProvider->setSort(
		['attributes' =>
//			['teamName' =>
//				[
//					'asc' => ['{{%robot}}.team.team_name' => SORT_ASC],
//					'desc' => ['{{%robot}}.team.team_name' => SORT_DESC],
//				],
//			],
			['robot.name' =>
				[
					'asc' => ['{{%robot}}.name' => SORT_ASC],
					'desc' => ['{{%robot}}.name' => SORT_DESC],
				],
			],
		]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
			'eventId' => $this->eventId,
        ]);

		// $query->andFilterWhere(['like', '{{%team}}.name', $this->getAttribute('robot.team.name')]);
		$query->andFilterWhere(['{{%robot}}.teamId' => $this->getAttribute('robot.teamId')]);

        return $dataProvider;
    }

}