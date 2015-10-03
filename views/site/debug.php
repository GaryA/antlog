<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Team;
use app\models\Robot;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $function;
$this->params['breadcrumbs'][] = $class;
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = 'Debug';
?>
<div class="debug-view">

    <h1><?= Html::encode($this->title) ?></h1>

<?php
	echo $debugName . ':<br><pre>';
	print_r($debugValue);
	echo '</pre>';
?>

</div>
