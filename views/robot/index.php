<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Robot;
use app\models\RobotSearch;
use app\models\RobotClass;
use app\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Robots';
$this->params['breadcrumbs'][] = $this->title;

$searchModel = New RobotSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>
<div class="robot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Robot', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'name',
            [
				'attribute' =>'teamId',
				'label' => 'Team',
				'filter' => User::teamDropdown(),
				'value' => function($model, $index, $dataColumn) {
					$teamDropdown = User::teamDropdown();
					return $teamDropdown[$model->teamId];
				},
			],
            [
				'attribute' => 'classId',
				'label' => 'Class',
				'filter' => RobotClass::dropdown(),
				'value' => function($model, $index, $dataColumn) {
					$classDropdown = RobotClass::dropdown();
					return $classDropdown[$model->classId];
				},
			],
			[
				'format' => 'raw',
				'label' => 'Signed Up',
				'value' => function($model, $index, $dataColumn) {
					/* figure out if robot is signed up */
					$checked = Robot::isSignedUp($model->id);
					return '<div><input type="checkbox" name="signup" value="true" disabled ' . $checked . '></div>';
				},
			],
            [
				'class' => 'yii\grid\ActionColumn',
				'buttons' =>
				[
					'delete' => function ($url, $model, $key)
					{
						if (($model->isUser($model) || User::isUserAdmin()) && $model->isOKToDelete($model->id))
						{
							return  Html::a('<span class="glyphicon glyphicon-trash"></span>', $url,
							[
                    			'title' => Yii::t('yii', 'Delete'),
                    			'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                    			'data-method' => 'post',
                    			'data-pjax' => '0',
							]);
						}
						else
						{
							return '';
						}
					},
					'update' => function ($url, $model, $key)
					{
						if (($model->isUser($model) || User::isUserAdmin()) && $model->isOKToEdit($model->id))
						{
							return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url,
							[
                				'title' => Yii::t('yii', 'Update'),
                				'data-pjax' => '0',
							]);
						}
						else
						{
							return '';
						}
					},
				],
			],
        ],
    ]); 
	
	if (isset(Yii::$app->request->queryParams['RobotSearch']['teamId']))
	{
		$param = Yii::$app->request->queryParams['RobotSearch']['teamId'];
		echo '<p>';
		echo Html::a('Sign Up Robots', ['entrant/index', 'EntrantSearch[robot.teamId]' => $param], ['class' => 'btn btn-success']);
		echo '</p>';
	}
	?>

</div>
