<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

$this->title = 'Import Database Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
<h1><?= Html::encode($this->title) ?></h1>

<?php
$form = ActiveForm::begin(['id' => 'upload_button_form', 'options' => ['data-target' => '../db/import', 'enctype' => 'multipart/form-data']]);

//echo $form->field($model, 'uploadFile')->fileInput();
echo $form->field($model, 'uploadFile')->widget(FileInput::classname(), ['pluginOptions' => ['showPreview' => false,]]);

ActiveForm::end();
?>
</div>
<div class="progress_form" style="display:none">
<?php
$form2 = ActiveForm::begin(['id' => 'form2', 'options' => ['data-target' => '../db/process']]);

echo Html::textInput('progress_key', uniqid(), ['id'=>'progress_key']);
echo Html::textInput('filename', null, ['id' => 'filename']);
echo Html::button('Submit', ['type' => 'submit', 'class' => 'btn btn-default']);

ActiveForm::end(); ?>
</div>
<div id="progress-wrapper" style="display:none">
<p>Processing, please wait...</p>
<?= yii\bootstrap\Progress::widget([
	'percent' => 99.999,
	'barOptions' => ['id' => 'progress-bar', 'class' => 'progress-bar-success progress-bar-striped active'],
//	'options' => ['id' => 'progress', 'class' => 'progress-bar-striped active']
])?>
</div>
<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/upload_button.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'upload_button_form');
?>
