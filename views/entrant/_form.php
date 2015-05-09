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
		$event = Event::findOne($eventId);
    	$eventField = $form->field($model, 'eventId');
		echo $eventField->dropDownList([$event->id => $event->name]);
	?>

    <?= $form->field($model, 'robotId')->dropDownList(Robot::dropdown(true, $eventId)) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
