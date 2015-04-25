<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Team;
use app\models\Robot;

/* @var $this yii\web\View */
/* @var $model app\models\Team */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Team', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'name',
        ],
    ]) ?>

</div>
<div>
<?php
	echo $debugName . ':<br><pre>';
	print_r($debugValue);
	echo '</pre>';
?>
</div>