<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Robot */

$this->title = 'Update Robot: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Robots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="robot-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    	'changeName' => $changeName,
		'changeTeam' => $changeTeam,
		'changeClass' => $changeClass,
		'changeType' => $changeType,
    	'retire' => $retire,
    ]) ?>

</div>
