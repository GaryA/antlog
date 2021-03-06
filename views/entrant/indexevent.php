<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use dosamigos\grid\GroupGridView;
use app\models\User;
use app\models\EntrantSearch;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $event->name . ' - Entrants';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = 'Entrants';

$searchModel = NULL;
?>
<div class="entrant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
	<?php
	if (($event->state == 'Registration') || ($event->state == 'Closed'))
	{
		$searchModel = New EntrantSearch();
		$entrantProvider = $searchModel->search(Yii::$app->request->queryParams);

		if (($event->state == 'Registration') || (Yii::$app->params['antlog_env'] == 'local'))
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
	}
	?>
    </p>

    <?php
    echo GroupGridView::widget([
        'dataProvider' => $entrantProvider,
    	'filterModel' => $searchModel,
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
				'attribute' => 'teamName',
				'label' => 'Team',
				'filter' => User::teamDropdown(),
    		],
            [
            	'attribute' => 'status',
            	'enableSorting' => false,
            	'format' => 'raw',
            	'filter' => [-1 => 'Signed Up', 2 => 'Entered'],
            	'value' => function($model, $index, $dataColumn) use ($event) {
					if (($event->state == 'Registration') || ($event->state == 'Closed' && Yii::$app->params['antlog_env'] == 'local'))
            		{
            			if ($model->status == -1)
            			{
            				$value = 'Signed Up';
            				if (User::isUserAdmin())
            				{
           					$value .= ' ' . Html::button('Enter',
            					[
            						'class' => 'btn btn-sm btn-success enter-btn',
            						'data-target' => Url::to(['enter', 'id' => $index]),
            					]);
             				}
            				if (User::isUserAdmin() || User::isCurrentUser($model->robot->teamId))
            				{
            					$value .= ' ' . Html::button('Delete',
            					[
            						'class' => 'btn btn-sm btn-danger delete-btn',
            						'data-target' => Url::to(['delete', 'eventId' => $event->id, 'id' => $index]),
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
            			else if ($model->status == 2)
            			{
            				$value = "Winners' Bracket";
            			}
            			else
            			{
            				$value = "Signed Up";
            			}
             		}
            		return $value;
            	},
            ],
        ],
    ]);
	?>
<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/entrants_buttons.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'entrants_form');
?>

</div>
