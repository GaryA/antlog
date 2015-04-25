<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Entrant */

$this->title = 'Update Entrant: ' . ' ' . $model->event->name . ' - ' . $model->robot->name;
$this->params['breadcrumbs'][] = ['label' => 'Entrants', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="entrant-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
