<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Entrant */

$this->title = 'New Entrant';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = ['label' => 'Entrants', 'url' => ['entrant/index', 'eventId' => $event->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entrant-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    	'eventId' => $event->id,
    	'status' => 2,
    ]) ?>

</div>
