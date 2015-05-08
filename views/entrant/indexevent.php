<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\grid\GroupGridView;
use app\models\Robot;
use app\models\Event;
use app\models\EntrantSearch;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$event = Event::findOne($eventId);

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
		echo Html::a('Add Entrant', ['create'], ['class' => 'btn btn-success']);
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
				'value' => function($model, $index, $dataColumn) {
					$robotDropdown = Robot::dropdown();
					return $robotDropdown[$model->robotId];
				},
			],
			[
				'attribute' => 'robot.team.team_name',
				'label' => 'Team',
				'filter' => User::teamDropdown(),
				'value' => function($model, $index, $dataColumn) {
					$teamDropdown = User::teamDropdown();
					return $teamDropdown[$model->robot->teamId];
				},
			],
            [
            	'attribute' => 'status',
            	'value' => function($model, $index, $dataColumn) {
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
            		return $value;
            	},
            ],
            [
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'delete' => function ($url, $model, $key)
					{
						if (User::isUserAdmin() && $model->isEditable($model->eventId))
						{
							return  Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
							[
                    			'title' => Yii::t('yii', 'Delete'),
                    			'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    			'data-method' => 'post',
                    			'data-pjax' => '0',
							]);
						}
						else
						{
							return '';
						}
					},
					'update' => function ($url, $model, $key)
					{
						if (User::isUserAdmin() && $model->isEditable($model->eventId))
						{
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,
							[
                				'title' => Yii::t('yii', 'Update'),
                				'data-pjax' => '0',
							]);
						}
						else
						{
							return '';
						}
					},
				],
			],
        ],
    ]);

	?>

</div>
