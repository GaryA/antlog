<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use app\models\RobotClass;
use app\models\Entrant;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

<?php $form = ActiveForm::begin();

echo $form->field($model, 'name')->textInput(['maxlength' => 100]);
echo $form->field($model, 'venue')->textInput(['maxlength' => 65535]);
echo Html::activeHiddenInput($model, 'organiserId', ['value' => Yii::$app->user->id]);
echo $form->field($model, 'eventDate')->widget(DatePicker::classname(),
	[
		'options' =>
		[
			'placeholder' => 'Enter event date ...',
			'value' => $model->eventDate,
		],
		'pluginOptions' =>
		[
			'startDate' => date('Y-m-d'),
			'todayHighlight' => true,
			'autoclose'=>true,
			'format' => 'yyyy-mm-dd',
		]
	]);

$list = $model->isNewRecord ? ['Future' => 'Future', 'Registration' => 'Registration'] : [$model->state => $model->state];
echo $form->field($model, 'state')->dropDownList($list);

if ($model->isOKToDelete($model->id))
{
	$list = ArrayHelper::map(RobotClass::find()
		->orderBy(['id' => SORT_DESC])
		->all(), 'id', 'name');
	echo $form->field($model, 'classId')->dropDownList($list)->label('Class');
}
else
{
	$list = ArrayHelper::map(RobotClass::find()
		->where(['id' => $model->classId])
		->all(), 'id', 'name');
	echo $form->field($model, 'classId')->dropDownList($list)->label('Class');
}
?>

<div class="form-group">
    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
</div>

<?php ActiveForm::end(); ?>

</div>
