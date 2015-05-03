<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\RobotClass;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Robot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="robot-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>

    <?php
	if (User::isUserAdmin())
	{
		echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown());
	}
	else
	{
		echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown(Yii::$app->user->identity->id));
	}
	?>

    <?= $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'type')->dropDownList(['' => '', 'Walker' => 'Walker', 'Cluster' => 'Cluster'])?>

    <?= $form->field($model, 'active')->dropDownList([0 => 'No', 1 => 'Yes']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
