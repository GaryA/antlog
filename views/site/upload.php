<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Import Database Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'uploadFile')->fileInput() ?>

    <button>Submit</button>
	<div id="progress-bar" class="progress" style="display:none">
		<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
		<span class="sr-only">Please wait...</span>
	</div>
</div>
<?php ActiveForm::end() ?>
</div>