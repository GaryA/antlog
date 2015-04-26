<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Teams';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
				'attribute' =>'username',
				'label' => 'Team',
        	],
            [
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'delete' => function ($url, $model, $key)
					{
						return ((User::isCurrentUser($model->id) || User::isUserAdmin()) && $model->isTeamEmpty($model->id)) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => 'Delete']) : '';
					},
					'update' => function ($url, $model, $key)
					{
						return (User::isCurrentUser($model->id) || User::isUserAdmin()) ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Update']) : '';
					},
				],
			],
        ],
    ]); ?>

</div>
