<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\Event;
use app\models\Team;
use app\models\EntrantSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sign Up';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['event/index']];
$this->params['breadcrumbs'][] = ['label' => $event->name, 'url' => ['event/view', 'id' => $event->id]];
$this->params['breadcrumbs'][] = ['label' => 'Entrants', 'url' => ['entrant/index', 'eventId' => $event->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="entrant-signup">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    	'eventId' => $event->id,
    	'status' => -1,
    ]) ?>

</div>
