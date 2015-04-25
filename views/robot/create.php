<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Robot */

$this->title = 'Create Robot';
$this->params['breadcrumbs'][] = ['label' => 'Robots', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="robot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
