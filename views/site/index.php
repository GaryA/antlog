<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\models\Robot;
use app\models\RobotSearch;
use app\models\RobotClass;
use app\models\User;

/* @var $this yii\web\View */
$this->title = 'AntLog 3.0';

?>
<div class="site-index">

	<div class="jumbotron">
		<h1>AntLog 3.0</h1>

		<p class="lead">Welcome to AntLog</p>

	</div>

	<div class="body-content">

		<div class="row">
			<div class="col-lg-4 col-md-6">
				<h2>
					<a class="btn btn-primary btn-block" href="index.php?r=event">Events</a>
				</h2>

<?php
$eventData->pagination =
[
	'defaultPageSize' => 5,
	'pageParam' => 'event-page' 
];
echo GridView::widget (
[
	'summary' => '',
	'dataProvider' => $eventData,
	'columns' =>
	[
		[ 
			'attribute' => 'name',
			'format' => 'raw',
			'value' => function ($model, $index, $dataColumn)
			{
				return Html::a ( $model->name,
					[ 
						'event/view',
						'id' => $model->id 
					] );
			} 
		],
		'state',
		[ 
			'attribute' => 'class.name',
			'label' => 'Class' 
		] 
	] 
] );
?>
			</div>
			<div class="col-lg-4 col-md-6">
				<h2>
					<a class="btn btn-primary btn-block" href="index.php?r=robot">Robots</a>
				</h2>
<?php
$robotData->pagination =
[ 
	'defaultPageSize' => 5,
	'pageParam' => 'robot-page' 
];
echo GridView::widget (
[
	'summary' => '',
	'dataProvider' => $robotData,
	'columns' =>
	[ 
		[ 
			'attribute' => 'name',
			'format' => 'raw',
			'value' => function ($model, $index, $dataColumn)
			{
				return Html::a ( $model->name,
					[ 
						'robot/view',
						'id' => $model->id 
					] );
			} 
		],
		[ 
			'attribute' => 'team.username',
			'label' => 'Team' 
		],
		[ 
			'attribute' => 'class.name',
			'label' => 'Class' 
		] 
	] 
] );
?>
            </div>
			<div class="col-lg-4 col-md-6">
				<h2>
					<a class="btn btn-primary btn-block" href="index.php?r=user">Teams</a>
				</h2>
<?php
$teamData->pagination =
[ 
	'defaultPageSize' => 5,
	'pageParam' => 'team-page' 
];
echo GridView::widget (
[ 
	'summary' => '',
	'dataProvider' => $teamData,
	'columns' =>
	[ 
		[ 
			'attribute' => 'username',
			'label' => 'Team',
			'format' => 'raw',
			'value' => function ($model, $index, $dataColumn)
			{
				return Html::a ( $model->username,
					[ 
						'user/view',
						'id' => $model->id 
					] );
			}
		]
	]
] );
?>
			</div>
			<div class="col-lg-6">
				<h2>Entrants</h2>

				<p>Use this link to view and administer entrants to the current
					event</p>

				<p>
					<a class="btn btn-primary btn-block" href="index.php?r=entrant">Entrants</a>
				</p>
			</div>
			<div class="col-lg-6">
				<h2>Fights</h2>

				<p>Use this link to run the fights of the current competition</p>

				<p>
					<a class="btn btn-primary btn-block" href="index.php?r=fights">Fights</a>
				</p>
			</div>
		</div>

	</div>
</div>
