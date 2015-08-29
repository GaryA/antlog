<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\RobotClass;
use app\models\RobotType;
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
    	echo $form->field($model, 'name')->textInput(['maxlength' => 100]);
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
		if ($model->isNewRecord) // and user is team not admin
		{
			echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown(Yii::$app->user->id), ['disabled' => 'disabled']);
			echo Html::activeHiddenInput($model, 'teamId', ['value' => Yii::$app->user->id]);
		}
		else // existing record so get team from model
		{
			echo $form->field($model, 'teamId')->dropDownList(User::teamDropdown($model->teamId), ['disabled' => 'disabled']);
			echo Html::activeHiddenInput($model, 'teamId', ['value' => $model->teamId]);
		}
	}

	if ($changeClass == true)
	{
    	echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()
    		->orderBy(['id' => SORT_DESC])
    		->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()
			->where(['id' => $model->classId])
			->all(), 'id', 'name'), ['disabled' => 'disabled']);
	}

	if ($changeType == true)
	{
    	echo $form->field($model, 'typeId')->dropDownList(ArrayHelper::map(RobotType::find()->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'typeId')->dropDownList(ArrayHelper::map(RobotType::find()
			->where(['id' => $model->typeId])
			->all(), 'id', 'name'), ['disabled' => 'disabled']);
	}

	if ($changeActive == true)
	{
    	echo $form->field($model, 'active')->dropDownList([1 => 'Yes', 0 => 'No']);
	}
	else
	{
		$active = ($model->active == 1) ? [1 => 'Yes'] : [0 => 'No'];
		echo $form->field($model, 'active')->dropDownList($active, ['disabled' => 'disabled']);
	}
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
