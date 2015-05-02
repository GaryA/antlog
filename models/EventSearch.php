<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Event;

/**
 * EventSearch represents the model behind the search form about `app\models\Event`.
 */
class EventSearch extends Event
{
	public function attributes()
	{
		return array_merge(parent::attributes(), ['class.name']);
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'classId'], 'integer'],
            [['name', 'class.name', 'eventDate'], 'safe'],
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
        $query = Event::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'sort'=> ['defaultOrder' => ['eventDate'=>SORT_DESC]]
        ]);

		$dataProvider->sort->attributes['class.name'] = [
			'asc' => ['{{%robot_class}}.name' => SORT_ASC],
			'desc' => ['{{%robot_class}}.name' => SORT_DESC],
		];

		$query->joinWith(['class']);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
             'classId' => $this->classId,
        ]);

        $query->andFilterWhere(['like', '{{%event}}.name', $this->name]);

		$query->andFilterWhere(['like', '{{%robot_class}}.name', $this->getAttribute('class.name')]);

        return $dataProvider;
    }

}