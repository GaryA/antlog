<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\RobotClass;
use app\models\EventSearch;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;

$searchModel = new EventSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<div class="event-index">

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
        <?= Html::a('New Event', ['create'], ['class' => 'btn btn-success'])?>
    </p>

    <?php
    echo GridView::widget(
    [
    	'dataProvider' => $dataProvider,
    	'filterModel' => $searchModel,
		'rowOptions' => function ($model, $index, $widget, $grid)
		{
			if ($model->state == 'Complete')
			{
				return
				[
					//'style'=>'color: #404040; background-color: #c0c0c0;'
					'class' => 'info'
				];
			}
			else
			{
				return [];
			}
		},
    	'columns' =>
    	[
    		[
    			'attribute' => 'name',
    			'format' => 'raw',
    			'value' => function($model, $index, $dataColumn)
    			{
    				return Html::a($model->name, ['view', 'id' => $model->id]);
    			},
    		],
    		[
    			'attribute' => 'eventDate',
    			'format' =>
    			[
    				'date',
    				'php:j M Y'
    			],
    		],
    		'state',
    		[
    			'attribute' => 'classId',
    			'label' => 'Class',
    			'filter' => RobotClass::dropdown(),
    			'value' => function ($model, $index, $dataColumn)
    			{
    				$classDropdown = RobotClass::dropdown();
    				return $classDropdown[$model->classId];
    			}
    		],
    	]
    ]);
	?>

</div>
