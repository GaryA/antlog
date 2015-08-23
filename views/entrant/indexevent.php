<?php

use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\grid\GroupGridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $event->name . ' - Entrants';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = 'Entrants';

?>
<div class="entrant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
	<?php
	if ($event->state == 'Registration')
	{
		if (User::isUserAdmin())
		{
			echo Html::a('Add Entrant', ['create', 'eventId' => $event->id], ['class' => 'btn btn-success']);
		}
		else
		{
			echo Html::a('Sign Up', ['signup', 'eventId' => $event->id], ['class' => 'btn btn-success']);
		}
	}
	?>
    </p>

    <?= GroupGridView::widget([
        'dataProvider' => $entrantProvider,
    	'mergeColumns' => ['group_num'],
    	'extraRowColumns' => ['group_num'],
    	'extraRowValue' => function ($model, $index, $totals)
    	{
    		return '<b>Group ' . $model->group_num . '</b>';
    	},
        'columns' => [
            [
				'attribute' =>'robot.name',
				'label' => 'Robot',
            	'format' => 'raw',
            	'value' => function($model, $index, $dataColumn)
            	{
            		return Html::a($model->robot->name,
            			[
            				'view',
            				'id' => $model->id,
            				'eventId' => $model->eventId
            			]);
            	}
    		],
			[
				'attribute' => 'robot.team.team_name',
				'label' => 'Team',
    		],
            [
            	'attribute' => 'status',
            	'enableSorting' => false,
            	'value' => function($model, $index, $dataColumn) use ($event) {
            		if ($event->state == 'Registration')
            		{
            			if ($model->status == 0)
            			{
            				$value = 'Signed Up';
            			}
            			else
            			{
            				$value = 'Entered';
            			}
            		}
            		else
            		{
            			if ($model->status == 0)
            			{
            				$value = 'Out';
            			}
            			else if ($model->status == 1)
            			{
            				$value = "Losers' Bracket";
            			}
            			else
            			{
            				$value = "Winners' Bracket";
            			}
             		}
            		return $value;
            	},
            ],

        ],
    ]);

	?>

</div>
