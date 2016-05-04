<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\Robot;
use app\models\Event;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Entrant */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entrant-form">

    <?php
    $form = ActiveForm::begin(['id' => 'add-entrant']);

	$event = Event::findOne($eventId);
    $eventField = $form->field($model, 'eventId');
	echo $eventField->dropDownList([$event->id => $event->name]);
	echo Html::activeHiddenInput($model, 'status', ['value' => $status]);
	echo Html::activeHiddenInput($event, 'classId', ['value' => $event['classId']]);
	if (!User::isUserAdmin())
	{
		$teamId = Yii::$app->user->identity->id;
	}
	else
	{
		$teamId = NULL;
	}
	$dropdown = Robot::dropdown(true, $eventId, $teamId);
	echo $form->field($model, 'robotId')->dropDownList($dropdown['robot'], $dropdown['class']);
	?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update',
        	['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
<?php Modal::begin([
    'id' => 'check-class-modal',
    'header' => '<h4 class="modal-title">Check Robot Class</h4>',
    'footer' => '<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>',

]); ?>
<div class="modal-center">
<?php
$form = ActiveForm::begin(['id' => 'check-class-form']);
echo Html::hiddenInput('target', NULL, ['id' => 'target']);
$eventClass = 'Antweight';
$robotClass = 'Fleaweight';
echo "<p>This competition is for <span id=\"event-class\"></span> robots.<br>";
echo "You are entering a <span id=\"robot-class\"></span>.<br>";
echo "Are you sure you want to do this?</p>";
ActiveForm::end();
echo Html::button('Yes', ['class' => 'btn btn-success', 'id' => 'button1']);
echo ' ';
echo Html::button('No', ['class' => 'btn btn-danger', 'id' => 'button2']);
?>
</div>

<?php Modal::end();

$this->registerJsFile(
	Yii::getAlias('@web') . '/js/check-entrant-class.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'add-entrant');
?>