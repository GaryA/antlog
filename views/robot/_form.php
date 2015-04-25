<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Team;
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
		echo $form->field($model, 'teamId')->dropDownList(ArrayHelper::map(Team::find()->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'teamId')->dropDownList(ArrayHelper::map(Team::find()->where(['userId' => Yii::$app->user->identity->id])->all(), 'id', 'name')); 
	}
	?>

    <?= $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->all(), 'id', 'name')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
