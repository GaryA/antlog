<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Fights */

$this->title = 'Update Fights: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fights', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fights-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
