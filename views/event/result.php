<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Team;
use app\models\Entrant;
use app\models\Event;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name . ' (' . $model->eventDate . ') Results';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['event/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Results';
?>
<div class="event-view">

	<h1><?= Html::encode($this->title) ?></h1>

    <?php
    echo DetailView::widget(
    	[
    		'model' => $model,
    		'attributes' =>
    		[
    			// 'id',
    			'name',
    			'state',
				'class.name'
			]
		]);
    ?>

</div>

<?php
$event = $model;

$query = Entrant::find()->where([
	'eventId' => $model->id
]);
$dataProvider = new ActiveDataProvider([
	'query' => $query,
	'sort' => [
		'defaultOrder' => [
			'finalFightId' => SORT_DESC
		]
	]
]);
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		[
			'attribute' => 'robot.name',
			'value' => function($model, $index, $dataColumn)
			{
				if ($model->robot->typeId == 0)
				{
					return $model->robot->name;
				}
				else
				{
					return $model->robot->name . ' (' . $model->robot->type->name . ')';
				}
			},
		],
		[
			'attribute' => 'robot.team.team_name',
			'label' => 'Team'
		],
		[
			'attribute' => 'finalFightId',
			'label' => 'Position',
			'value' => function ($model, $index, $dataColumn)
			{
				return Event::getPosition($model->finalFightId, $model->eventId);
			}
		]
/*			[
				'attribute' => 'classId',
				'label' => 'Class',
				'filter' => RobotClass::dropdown(),
				'value' => function($model, $index, $dataColumn) {
					$classDropdown = RobotClass::dropdown();
					return $classDropdown[$model->classId];
				},
			],
*/
		]
]);
?>
