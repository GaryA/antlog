<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Fights;

/**
 * FightsSearch represents the model behind the search form about `app\models\Fights`.
 */
class FightsSearch extends Fights
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fightGroup', 'fightRound', 'fightNo', 'robot1Id', 'robot2Id', 'winnerId', 'loserId', 'winnerNextFight', 'loserNextFight', 'sequence'], 'integer'],
            [['fightBracket'], 'safe'],
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
        $query = Fights::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fightGroup' => $this->fightGroup,
            'fightRound' => $this->fightRound,
            'fightNo' => $this->fightNo,
            'robot1Id' => $this->robot1Id,
            'robot2Id' => $this->robot2Id,
            'winnerId' => -1,
            'loserId' => -1,
            'sequence' => $this->sequence,
        ]);

        $query->andFilterWhere(['like', 'fightBracket', $this->fightBracket]);
        $query->andFilterWhere(['>', 'robot1Id', 0]);
        $query->andFilterWhere(['>', 'robot2Id', 0]);

        return $dataProvider;
    }
}
