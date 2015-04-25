<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Team */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
		if ($model->isUser($model) || User::isUserAdmin())
		{
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
		}
		if ($model->isEmpty($model->id))
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
		?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
        ],
    ]) ?>

</div>
