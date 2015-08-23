<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Entrant */

$this->title = 'Update Entrant: ' . ' ' . $model->event->name . ' - ' . $model->robot->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = ['label' => 'Entrants', 'url' => ['entrant/index', 'eventId' => $event->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="entrant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    	'eventId' => $event->id,
    	'status' => $model->status,
    ]) ?>

</div>
