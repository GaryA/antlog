<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\RobotSearch;
use app\models\RobotClass;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Robots';
$this->params['breadcrumbs'][] = $this->title;

$searchModel = New RobotSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<div class="robot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('New Robot', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
    	'rowOptions' => function ($model, $index, $widget, $grid)
    	{
    		return $model->active ? [] : ['class' => 'info'];
    	},
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
            	'attribute' => 'name',
            	'format' => 'raw',
            	'value' => function($model, $index, $dataColumn)
            	{
            		return Html::a($model->name, ['view', 'id' => $model->id]);
            	}
            ],
        	'type.name',
            [
				'attribute' =>'teamId',
				'label' => 'Team',
				'filter' => User::teamDropdown(),
				'value' => function($model, $index, $dataColumn) {
					$teamDropdown = User::teamDropdown();
					return $teamDropdown[$model->teamId];
				},
			],
            [
				'attribute' => 'classId',
				'label' => 'Class',
				'filter' => RobotClass::dropdown(),
				'value' => function($model, $index, $dataColumn) {
					$classDropdown = RobotClass::dropdown();
					return $classDropdown[$model->classId];
				},
			],
			[
				'format' => 'raw',
				'label' => 'Signed Up',
				'value' => function($model, $index, $dataColumn) {
					/* figure out if robot is signed up */
					$checked = Robot::isSignedUp($model->id);
					return '<div><input type="checkbox" name="signup" value="true" disabled ' . $checked . '></div>';
				},
			],
        ],
    ]);

	?>

</div>
