<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use app\models\User;
use dosamigos\grid\GroupGridView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->team_name;
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
		if ((User::isCurrentUser($model->id)) || User::isUserAdmin())
		{
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);

			if ($model->isTeamEmpty($model->id))
			{
				echo Html::a('Delete', ['delete', 'id' => $model->id],
				[
					'class' => 'btn btn-danger',
					'data' =>
					[
						'confirm' => 'Are you sure you want to delete this item?',
						'method' => 'post',
					],
				]);
			}
		}
		?>
    </p>

<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'username',
    ],
]);
Pjax::begin();
echo GroupGridView::widget([
	'dataProvider' => $robots,
	'mergeColumns' => ['classId'],
	'type' => GroupGridView::MERGE_SIMPLE,
	'extraRowColumns' => ['classId'],
	'extraRowValue' => function($model, $index, $totals)
	{
		return '<b>' . $model->class->name . '</b>';
	},
	'columns' =>
	[
       	'name',
		'type.name',
       	[
			'attribute' => 'active',
       		'format' => 'boolean',
 		]
	],
]);
Pjax::end();
Pjax::begin();
if ($nextFights->count > 0)
{
	echo '<h2>Up-coming fights</h2>';
	groupGridView($nextFights);
}
Pjax::end();
//echo '<pre>';
//echo print_r($fights);
//echo '</pre>';
Pjax::begin();
if (count($fights) > 0)
{
	echo '<h2>Previous fights</h2>';
	echo '<div class="panel-group" id="accordion">';
	$index = 0;
	foreach ($fights as $robot => $fight)
	{
		echo '<div class="panel panel-default">';
		echo '<div class="panel-heading">';

		echo '<h3 class="panel-title">';
		echo Html::a($robot, ["#panel$index"], ['data-toggle' => "collapse", 'data-parent' => "#accordion"]);
		echo '</h3>';
		echo '</div>';
		echo '<div id="panel' . $index . '" class="panel-collapse collapse">';
		$index++;
		echo '<div class="panel-body">';
		$eventCount = 0;
		foreach ($fight as $event => $details)
		{
			if ($details->count > 0)
			{
				echo "<h4>$event</h4>";
				groupGridView($details);
				$eventCount++;
			}
		}
		if ($eventCount == 0)
		{
			echo '<b>No previous fights to display</b><br>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	echo'</div>';
}
Pjax::end();

function groupGridView($provider)
{
	echo GroupGridView::widget([
		'dataProvider' => $provider,
		'mergeColumns' => ['fightRound', 'fightBracket', 'fightGroup'],
		'type' => GroupGridView::MERGE_NESTED,
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
			else
			{
				if ($model->fightBracket == 'W')
				{
					$bracket = "Winners' bracket";
				}
				else
				{
					$bracket = "Losers' bracket";
				}
				if ($model->fightGroup == 9)
				{
					$retVal = "Finals Round $model->fightRound, $bracket";
				}
				else
				{
					$retVal = "Group $model->fightGroup Round $model->fightRound, $bracket";
				}
			}
			return $retVal;
		},
		'columns' => [
			[
				'attribute' => 'robot1.robot.team.team_name',
				'contentOptions' => ['class' => 'groupview-right-align'],
				'headerOptions' => ['class' => 'groupview-right-align'],
				'label' => 'Team',
			],
			[
				'attribute' => 'robot1.robot.name',
				'format' => 'raw',
				'contentOptions' => ['class' => 'groupview-right-align'],
				'headerOptions' => ['class' => 'groupview-right-align'],
				'value' => function($model, $index, $dataColumn)
				{
					if ($model->robot1Id > 0)
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
				'label' => 'vs',
				'contentOptions' => ['class' => 'groupview-center-align'],
				'headerOptions' => ['class' => 'groupview-center-align'],
				'format' => 'html',
				'content' => function($model, $index, $dataColumn)
				{
					return 'vs';
				},
			],
			[
				'attribute' => 'robot2.robot.name',
				'format' => 'raw',
				'value' => function($model, $index, $dataColumn)
				{
					if ($model->robot2Id > 0)
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
			[
				'attribute' => 'robot2.robot.team.team_name',
				'label' => 'Team',
			],
		],
	]);
}
?>

</div>
