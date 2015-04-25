<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Fights */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fights-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fightGroup')->textInput() ?>

    <?= $form->field($model, 'fightRound')->textInput() ?>

    <?= $form->field($model, 'fightBracket')->textInput(['maxlength' => 0]) ?>

    <?= $form->field($model, 'fightNo')->textInput() ?>

    <?= $form->field($model, 'robot1Id')->textInput() ?>

    <?= $form->field($model, 'robot2Id')->textInput() ?>

    <?= $form->field($model, 'winnerId')->textInput(['value' => 0]) ?>

    <?= $form->field($model, 'loserId')->textInput() ?>

    <?= $form->field($model, 'winnerNextFight')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'loserNextFight')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'sequence')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
