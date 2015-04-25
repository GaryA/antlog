<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Team;
use app\models\Robot;
use app\models\Event;

/* @var $this yii\web\View */
/* @var $model app\models\Entrant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entrant-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
		$eventField = $form->field($model, 'eventId');
		if ($model->isNewRecord)
		{
			echo $eventField->dropDownList(ArrayHelper::map(Event::find()->where(['state' => 'Registration'])->all(), 'id', 'name'));
		}
		else
		{
			echo $eventField->textInput(['value' => $model->event->name, 'disabled' => 'true']);
		}
	?>

    <?= $form->field($model, 'robotId')->dropDownList(ArrayHelper::map(Robot::find()->all(), 'id', 'name')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
