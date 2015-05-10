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

    <?php
    $form = ActiveForm::begin();
	if ($changeName == true)
	{
    	echo $form->field($model, 'name')->textInput(['maxlength' => 50]);
	}
	else
	{
		echo $form->field($model, 'name')->textInput(['value' => $model->name, 'disabled' => true]);
	}

	if (User::isUserAdmin() && ($changeTeam == true))
	{
		echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown());
	}
	else
	{
		echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown(Yii::$app->user->identity->id));
	}

	if ($changeClass == true)
	{
    	echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()
			->where(['id' => $model->classId])
			->all(), 'id', 'name'));
	}

	if ($changeType == true)
	{
    	echo $form->field($model, 'type')->dropDownList(['' => '', 'Walker' => 'Walker', 'Cluster' => 'Cluster']);
	}
	else
	{
		echo $form->field($model, 'type')->textInput(['value'=>$model->type, 'disabled' => true]);
	}

	if ($retire == true)
	{
    	echo $form->field($model, 'active')->dropDownList([0 => 'No', 1 => 'Yes']);
	}
	else
	{
		$active = ($model->active == 1) ? [1 => 'Yes'] : [0 => 'No'];
		echo $form->field($model, 'active')->dropDownList($active);
	}
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
