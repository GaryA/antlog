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
    	'rowOptions' => function ($model, $index, $widget, $grid)
    	{
    		return ($model->status == User::STATUS_ACTIVE) ? [] : ['class' => 'info'];
    	},
    	'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
				'attribute' =>'username',
				'label' => 'User Name',
            	'format' => 'raw',
            	'value' => function($model, $index, $dataColumn)
            	{
            		return Html::a($model->username, ['view', 'id' => $model->id]);
            	}
        	],
        	[
        		'attribute' => 'team_name',
        		'label' => 'Team Name'
        	],
        ],
    ]); ?>

</div>
