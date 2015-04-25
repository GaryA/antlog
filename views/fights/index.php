<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FightsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fights';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fights-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'fightRound',
            'fightBracket',
            'fightGroup',
            //'fightNo',
			'robot1.robot.team.name',
			[
				'attribute' => 'robot1.robot.name',
				'format' => 'raw',
				'value' => function($model, $index, $dataColumn) {
					return '<a href = index.php?r=fights/update&id=' . $index . '&winner=' . $model->robot1->id . '>' . $model->robot1->robot->name . '</a>';
				},
			],
			'robot2.robot.team.name',
			[
				'attribute' => 'robot2.robot.name',
				'format' => 'raw',
				'value' => function($model, $index, $dataColumn) {
					return '<a href = index.php?r=fights/update&id=' . $index . '&winner=' . $model->robot2->id . '>' . $model->robot2->robot->name . '</a>';
				},
			],
			// 'winnerId',
            // 'loserId',
            // 'winnerNextFight',
            // 'loserNextFight',
            // 'sequence',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
