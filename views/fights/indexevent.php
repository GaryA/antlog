<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\Event;
use app\models\User;
use app\models\Fights;
use yii\widgets\ActiveForm;
use dosamigos\grid\GroupGridView;
use yii\bootstrap\Modal;

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
    <p>Winners shown in <b>bold</b>
	<?php
	if ($state != 'Complete')
	{
		if ($complete == 1)
		{
			echo Html::a('Hide complete fights',['index', 'eventId' => $event->id, 'byes' => $byes, 'complete' => 0]);
		}
		elseif ($complete == 0)
		{
			echo Html::a('Show complete fights',['index', 'eventId' => $event->id, 'byes' => $byes, 'complete' => 1]);
		}
	}
	?>
    </p>

    <?php
    ActiveForm::begin(['id' => 'fight_button_form']);

    echo GroupGridView::widget([
        'dataProvider' => $fightsProvider,
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
				'content' => function($model, $index, $dataColumn) use ($event)
				{
					if (User::isUserAdmin() && ($event->state !== 'Complete') && ($model->robot1Id > 0) && ($model->robot2Id > 0))
					{
						$team1 = $model->robot1->robot->team->team_name;
						$robot1name = $model->robot1->robot->name;
						$entrant1 = $model->robot1->id;
						$team2 = $model->robot2->robot->team->team_name;
						$robot2name= $model->robot2->robot->name;
						$entrant2 = $model->robot2->id;
						if ($model->winnerId == -1)
						{
							return Html::button('vs', [
								'class' => 'btn btn-primary',
								'data-toggle' => 'modal',
								'data-target' => '#run-fight-modal',
								'data-button-target' => '../fights/update',
								'data-pjax' => '0',
								'data-team1' => $team1,
								'data-robot1name'=> $robot1name,
								'data-entrant1' => $entrant1,
								'data-team2' => $team2,
								'data-robot2name'=> $robot2name,
								'data-entrant2' => $entrant2,
								'data-id' => $index,
								'data-title' => Fights::labelRound($model),
							]);
						}
						else
						{
							return Html::button('change', [
								'class' => 'btn btn-danger',
								'data-toggle' => 'modal',
								'data-target' => '#change-result',
								'data-button-target' => '../fights/check',
								'data-pjax' => '0',
								'data-team1' => $team1,
								'data-robot1name'=> $robot1name,
								'data-entrant1' => $entrant1,
								'data-team2' => $team2,
								'data-robot2name'=> $robot2name,
								'data-entrant2' => $entrant2,
								'data-winner-id' => $model->winnerId,
								'data-button-update' => '../fights/update',
								'data-id' => $index,
								'data-title' => Fights::labelRound($model),
							]);
						}
					}
					else
					{
						return 'vs';
					}
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
	ActiveForm::end();
	?>

</div>
<?php Modal::begin([
    'id' => 'run-fight-modal',
    'header' => '<h4 class="modal-title">Current Fight</h4>',
    'footer' => '<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>',

]); ?>

<div class="modal-center">
<?php
$form = ActiveForm::begin(['id' => 'fight-form']);
echo Html::hiddenInput('target', NULL, ['id' => 'target']);
echo Html::hiddenInput('fight', NULL, ['id' => 'fight']);
echo Html::hiddenInput('entrant1', NULL, ['id' => 'entrant1']);
echo Html::hiddenInput('entrant2', NULL, ['id' => 'entrant2']);
echo Html::hiddenInput('winner', NULL, ['id' => 'winner']);
ActiveForm::end();
echo Html::button('', ['class' => 'btn btn-primary btn-fight', 'id' => 'button1']);
echo ' vs ';
echo Html::button('', ['class' => 'btn btn-primary btn-fight', 'id' => 'button2']);
?>
</div>

<?php Modal::end(); ?>

<?php Modal::begin([
	'id' => 'change-result',
	'header' => '<h4 class="modal-title">Change Result</h4>',
	'footer' => '<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>',
]); ?>
<div class="modal-center">
<?php
$form = ActiveForm::begin(['id' => 'change-form']);
echo Html::hiddenInput('target', NULL, ['id' => 'change-target']);
echo Html::hiddenInput('fight', NULL, ['id' => 'change-fight']);
echo Html::hiddenInput('entrant1', NULL, ['id' => 'change-entrant1']);
echo Html::hiddenInput('entrant2', NULL, ['id' => 'change-entrant2']);

ActiveForm::end();
echo Html::button('', ['class' => 'btn btn-fight', 'id' => 'change-button']);
?>
</div>
<?php Modal::end(); ?>

<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/run_fight_button.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'fight_button_form');
?>
