<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FightsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fights-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fightGroup') ?>

    <?= $form->field($model, 'fightRound') ?>

    <?= $form->field($model, 'fightBracket') ?>

    <?= $form->field($model, 'fightNo') ?>

    <?php // echo $form->field($model, 'robot1Id') ?>

    <?php // echo $form->field($model, 'robot2Id') ?>

    <?php // echo $form->field($model, 'winnerId') ?>

    <?php // echo $form->field($model, 'loserId') ?>

    <?php // echo $form->field($model, 'winnerNextFight') ?>

    <?php // echo $form->field($model, 'loserNextFight') ?>

    <?php // echo $form->field($model, 'sequence') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
