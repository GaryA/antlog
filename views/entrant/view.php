<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Entrant */

$this->title = $model->event->name . ' - ' . $model->robot->name;
$this->params['breadcrumbs'][] = ['label' => 'Entrants', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
	<?php
        if (($model->event->state == 'Registration') && User::isUserAdmin())
		{
			echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
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
            // 'id',
            [
				'label' => 'Event',
				'value' => $model->event->name,
			],
        	'group_num',
            [
				'label' => 'Robot',
				'value' => $model->robot->name,
			],
			[
				'label' => 'Team',
				'value' => $model->robot->team->team_name,
			],
            'status',
        ],
    ]) ?>

</div>
