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
				'label' => 'User Name',
        	],
        	[
        		'attribute' => 'team_name',
        		'label' => 'Team Name'		
        	],
            [
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'delete' => function ($url, $model, $key)
					{
						if ((User::isCurrentUser($model->id) || User::isUserAdmin()) && $model->isTeamEmpty($model->id))
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
						if (User::isCurrentUser($model->id) || User::isUserAdmin())
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
    ]); ?>

</div>
