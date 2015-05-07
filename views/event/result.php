<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Team;
use app\models\Entrant;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
	'label' => 'Events',
	'url' => [
		'index'
	]
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

	<h1><?= Html::encode($this->title) . ' (' . $model->eventDate . ') Results' ?></h1>

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
				if ($model->robot->type == '')
				{
					return $model->robot->name;
				}
				else
				{
					return $model->robot->name . ' (' . $model->robot->type . ')';
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
				switch ($model->finalFightId)
				{
					case 256:
						$position = '1st';
						break;
					case 255:
					case 254:
						$position = '2nd';
						break;
					case 253:
						$position = '3rd';
						break;
					case 252:
						$position = '4th';
						break;
					case 251:
					case 250:
						$position = 'Joint 5th';
						break;
					case 249:
					case 248:
						$position = 'Joint 7th';
						break;
					case 243:
					case 244:
					case 245:
					case 246:
						$position = 'Joint 9th';
						break;
					case 237:
					case 238:
					case 239:
					case 240:
						$position = 'Joint 13th';
						break;
					default:
						$position = '';
						break;
				}
				return $position;
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
