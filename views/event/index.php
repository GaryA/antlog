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

$searchModel = New EventSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Event', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            'name',
            'state',
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
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'delete' => function ($url, $model, $key)
					{
						return ((User::isUserAdmin()) && $model->isOKToDelete($model->id)) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => 'Delete']) : '';
					},
					'update' => function ($url, $model, $key)
					{
						return ((User::isUserAdmin()) && $model->state == 'Registration') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Update']) : '';
					},
				],
			],
        ],
    ]); ?>

</div>
