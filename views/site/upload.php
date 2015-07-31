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

<?php ActiveForm::end() ?>
</div>