<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\Event;
use app\models\Team;
use app\models\EntrantSearch;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Entrants';
$this->params['breadcrumbs'][] = $this->title;

$searchModel = New EntrantSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<div class="entrant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		This would be the signup page.
    </p>

</div>
