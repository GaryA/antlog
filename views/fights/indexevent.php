<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\Event;
use app\models\User;
use dosamigos\grid\GroupGridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FightsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$event = Event::findOne($eventId);

$this->title = 'Fights';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fights-index">

    <h1><?= Html::encode($this->title . ' - ' . $event->name) ?></h1>
    <p>Winners shown in <b>bold</b></p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GroupGridView::widget([
        'dataProvider' => $fightsProvider,
    	'mergeColumns' => ['fightRound', 'fightBracket', 'fightGroup'],
    	'type' => GroupGridView::MERGE_SIMPLE,
    	'extraRowColumns' => ['fightRound', 'fightBracket', 'fightGroup'],
		'extraRowValue' => function($model, $index, $totals)
		{
			if ($model->fightRound == 15)
			{
				$retVal = "Final (replay)";
			}
			else if ($model->fightRound == 14)
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
						if (($model->winnerId == -1) && (User::isUserAdmin()))
						{
							return Html::a($model->robot1->robot->name, ['/fights/update', 'id' => $index, 'winner' => $model->robot1->id]);
						}
						else
						{
							$prefix = '';
							$suffix = '';
							if ($model->robot1Id == $model->winnerId)
							{
								$prefix = '<b>';
								$suffix = '</b>';
							}
							return $prefix . $model->robot1->robot->name . $suffix;
						}
					}
					else if ($model->robot1Id == 0)
					{
						return '-- BYE --';
					}
					else
					{
						return '-- UNKNOWN --';
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
						if (($model->winnerId == -1) && (User::isUserAdmin()))
						{
							return Html::a($model->robot2->robot->name, ['/fights/update', 'id' => $index, 'winner' => $model->robot2->id]);
						}
						else
						{
							$prefix = '';
							$suffix = '';
							if ($model->robot2Id == $model->winnerId)
							{
								$prefix = '<b>';
								$suffix = '</b>';
							}
							return $prefix . $model->robot2->robot->name . $suffix;
						}
					}
					else if ($model->robot2Id == 0)
					{
						return '-- BYE --';
					}
					else
					{
						return '-- UNKNOWN --';
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
