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
		echo Html::a('Add Entrant', ['create', 'eventId' => $event->id], ['class' => 'btn btn-success']);
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
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'view' => function($url, $model, $key)
					{
						$url = Url::toRoute(['view', 'id' => $model->id, 'eventId' => $model->eventId]);
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url,
							[
								'title' => Yii::t('yii', 'View'),
								'data-pjax' => '0',
							]);
					},
					'delete' => function ($url, $model, $key)
					{
						if (User::isUserAdmin() && $model->isEditable($model->eventId))
						{
							$url = Url::toRoute(['delete', 'id' => $model->id, 'eventId' => $model->eventId]);
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
							$url = Url::toRoute(['update', 'id' => $model->id, 'eventId' => $model->eventId]);
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
            [
				'attribute' =>'robot.name',
				'label' => 'Robot',
    		],
			[
				'attribute' => 'robot.team.team_name',
				'label' => 'Team',
    		],
            [
            	'attribute' => 'status',
            	'enableSorting' => false,
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

        ],
    ]);

	?>

</div>
