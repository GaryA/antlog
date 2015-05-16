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

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 20])?>

<?php
echo $form->field($model, 'eventDate')->widget(DatePicker::classname(),
	[
		'options' =>
		[
			'placeholder' => 'Enter event date ...',
			'value' => $model->eventDate,
		],
		'pluginOptions' =>
		[
			'todayHighlight' => true,
			'autoclose'=>true,
			'format' => 'yyyy-mm-dd',
		]
	]);

	$list = $model->isNewRecord ? ['Future' => 'Future'] : [$model->state => $model->state];
	echo $form->field($model, 'state')->dropDownList($list);

	if ($model->isOKToDelete($model->id))
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()->all(), 'id', 'name'));
	}
	else
	{
		echo $form->field($model, 'classId')->dropDownList(ArrayHelper::map(RobotClass::find()
			->where(['id' => $model->classId])
			->all(), 'id', 'name'));
	}
?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
