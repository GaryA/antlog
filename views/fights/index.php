<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use dosamigos\grid\GroupGridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FightsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fights';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fights-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GroupGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
    	'mergeColumns' => ['fightRound', 'fightBracket', 'fightGroup'],
    	'type' => GroupGridView::MERGE_SIMPLE,
    	'extraRowColumns' => ['fightRound', 'fightBracket', 'fightGroup'],
		'extraRowValue' => function($model, $index, $totals)
		{
			if ($model->fightRound == 14)
			{
				$retVal = "Final";
			}
			else if ($model->fightRound == 13)
			{
				$retVal = "Third Place Play-off";
			}
			else if ($model->fightGroup == 9)
			{
				$retVal = "Finals Round $model->fightRound, $model->fightBracket bracket";
			}
			else
			{
				$retVal = "Group $model->fightGroup Round $model->fightRound, $model->fightBracket bracket";
			}
			return $retVal;
		},
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            //'fightRound',
            //'fightBracket',
            //'fightGroup',
            //'fightNo',
			[
				'attribute' => 'robot1.robot.team.team_name',
				'label' => 'Team',
			],
			[
				'attribute' => 'robot1.robot.name',
				'format' => 'raw',
				'value' => function($model, $index, $dataColumn)
				{
					if ($model->robot1Id > 0)
					{
						return '<a href = index.php?r=fights/update&id=' . $index . '&winner=' . $model->robot1->id . '>' . $model->robot1->robot->name . '</a>';
					}
					else
					{
						return '-- BYE --';
					}
				},
			],
			[
				'attribute' => 'robot2.robot.team.team_name',
				'label' => 'Team',
			],
			[
				'attribute' => 'robot2.robot.name',
				'format' => 'raw',
				'value' => function($model, $index, $dataColumn)
				{
					if ($model->robot2Id > 0)
					{
						return '<a href = index.php?r=fights/update&id=' . $index . '&winner=' . $model->robot2->id . '>' . $model->robot2->robot->name . '</a>';
					}
					else
					{
						return '-- BYE --';
					}
				},
			],
			// 'winnerId',
            // 'loserId',
            // 'winnerNextFight',
            // 'loserNextFight',
            // 'sequence',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
