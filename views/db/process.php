<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Import Database Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
<h1><?= Html::encode($this->title) ?></h1>

<?php
$form = ActiveForm::begin(['id' => 'process_db_form']);
echo Html::hiddenInput('progress_key', uniqid(), ['id'=>'progress_key']);
echo Html::hiddenInput('filename', $fileName, ['id'=>'filename']);
ActiveForm::end()
?>
</div>
<div id="progress-wrapper" style="display:none">
<p>Processing, please wait...</p>
<?= yii\bootstrap\Progress::widget([
	'percent' => 0,
	'barOptions' => ['id' => 'progress-bar', 'class' => 'progress-bar-success'],
	'options' => ['id' => 'progress', 'class' => 'active progress-bar-striped']
])?>
</div>
<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/upload_button.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'process_db_form');
?>
