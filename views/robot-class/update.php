<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RobotClass */

$this->title = 'Update Robot Class: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Robot Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="robot-class-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
