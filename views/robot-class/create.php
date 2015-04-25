<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\RobotClass */

$this->title = 'Create Robot Class';
$this->params['breadcrumbs'][] = ['label' => 'Robot Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="robot-class-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
