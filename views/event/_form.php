<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\RobotClass;
use app\models\Entrant;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 20]) ?>

	<?= $form->field($model, 'state')->textInput(['value' => $model->state, 'disabled' => 'true']) ?>

    <?php
	if ($model->isOKToDelete($model->id))
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->where(['id' => $model->classId])->all(), 'id', 'name'));
	}
	?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
