<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use app\models\User;
use dosamigos\grid\GroupGridView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->team_name;
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
		if ((User::isCurrentUser($model->id)) || User::isUserAdmin())
		{
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);

			if ($model->isTeamEmpty($model->id))
			{
				echo Html::a('Delete', ['delete', 'id' => $model->id],
				[
					'class' => 'btn btn-danger',
					'data' =>
					[
						'confirm' => 'Are you sure you want to delete this item?',
						'method' => 'post',
					],
				]);
			}
		}
		?>
    </p>

<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'username',
    ],
]);
Pjax::begin();
echo GroupGridView::widget([
	'dataProvider' => $robots,
	'mergeColumns' => ['classId'],
	'type' => GroupGridView::MERGE_SIMPLE,
	'extraRowColumns' => ['classId'],
	'extraRowValue' => function($model, $index, $totals)
	{
		return '<b>' . $model->class->name . '</b>';
	},
	'columns' =>
	[
       	'name',
		'type.name',
       	[
			'attribute' => 'active',
       		'format' => 'boolean',
 		]
	],
]);
Pjax::end();
?>

</div>
