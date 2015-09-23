<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use dosamigos\grid\GroupGridView;
use app\models\User;
use app\models\EntrantSearch;

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
		$searchModel = New EntrantSearch();
		$entrantProvider = $searchModel->search(Yii::$app->request->queryParams);

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

    <?php
    Pjax::begin();
    echo GroupGridView::widget([
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
				//'attribute' => 'robot.team.team_name',
				'attribute' => 'teamName',
				'label' => 'Team',
    		],
            [
            	'attribute' => 'status',
            	'enableSorting' => false,
            	'format' => 'raw',
            	'value' => function($model, $index, $dataColumn) use ($event) {
            		if ($event->state == 'Registration')
            		{
            			if ($model->status == -1)
            			{
            				$value = 'Signed Up';
            				if (User::isUserAdmin())
            				{
            					$value .= ' ' . Html::a('Enter', ['enter', 'eventId' => $event->id, 'id' => $index],
            					['class' => 'btn btn-sm btn-success',
            					'data' => ['method' => 'post',]]);
            				}
            				if (User::isUserAdmin() || User::isCurrentUser($model->robot->teamId))
            				{
            					$value .= ' ' . Html::a('Delete', ['delete', 'eventId' => $event->id, 'id' => $index],
            						['class' => 'btn btn-sm btn-danger',
            							'data' => ['method' => 'post',
            							'confirm' => 'Are you sure you want to delete this entry?'],
            					]);
            				}
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
	Pjax::end();
	?>

</div>
