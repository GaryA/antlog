<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\models\User;
use app\models\Entrant;

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

	<h1><?= Html::encode($this->title) ?></h1>

	<p>
<?php
	$form = ActiveForm::begin(['id' => 'event_button_form']);

	if (User::isUserAdmin())
	{
		if ($model->isOKToDelete($model->id))
		{
			echo Html::a('Delete', ['delete', 'id' => $model->id],
				['class' => 'btn btn-danger',
					'data' => [
						'confirm' => 'Are you sure you want to delete this item?',
						'method' => 'post'
					]
				]);
		}
		if ($model->state == 'Future')
		{
			echo Html::a('Open', ['open', 'id' => $model->id],
				['class' => 'btn btn-primary']);
		}
		else if ($model->state == 'Registration')
		{
			echo Html::a('Update', ['update', 'id' => $model->id],
				['class' => 'btn btn-primary']);
			echo Html::a('Do Draw', false, ['data-target' => 'event/draw', 'class' => 'do_draw btn btn-primary']);
		}
		else if ($model->state == 'Setup')
		{
			echo Html::a('Re-Do Draw', false, ['data-target' => 'event/setup', 'class' => 'do_draw btn btn-primary']);
		}
		else if ($model->state == 'Running')
		{
			echo Html::a('Run Fights', false, ['data-target' => 'event/run', 'class' => 'do_draw btn btn-primary']);
		}
		echo Html::hiddenInput('progress_key', uniqid(), ['id'=>'progress_key']);
		echo Html::hiddenInput('id', $model->id, ['id' => 'event_id']);
	}
	echo Html::a('Entrants', ['/entrant', 'eventId' => $model->id],
		['class' => 'btn btn-primary']);
	if (($model->state == 'Running') || ($model->state == 'Complete'))
	{
		echo Html::a('Fights', ['/fights', 'eventId' => $model->id],
			['class' => 'btn btn-primary']);
	}
	if ($model->state == 'Complete')
	{
		echo Html::a('Results', ['result', 'id' => $model->id],
			['class' => 'btn btn-info']);
	}

	ActiveForm::end();
?>
    </p>
<p>
<?= $this->render('_progressbar')?>

</p>
    <?php
    echo DetailView::widget(
    	[
    		'model' => $model,
    		'attributes' =>
    		[
    			'name',
    			[
    				'attribute' => 'eventDate',
    				'format' =>
    				[
    					'date',
    					'php:j M Y'
    				],
				],
       			'state',
    			'class.name'
    		]
    	]);
    ?>

</div>

<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/do_draw_button.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__.'#'.'event_button_form');
?>

<div>
<table class="table table-striped table-bordered detail-view">
		<tr>
			<th>Team</th>
			<th>Robots</th>
			<th>No. Entries</th>
		</tr>
<?php
foreach ($teams as $team => $robots)
{
	echo '<tr><td>' . User::findOne($team)->team_name . '</td><td>';
	foreach ($robots as $robot)
	{
		$model = Entrant::findOne($robot);
		if ($model->robot->typeId == 0)
		{
			echo $model->robot->name . '<br>';
		}
		else
		{
			echo $model->robot->name . ' (' . $model->robot->type->name . ')<br>';
		}
	}
	echo '</td><td>' . count($robots) . '</td></tr>';
}
?>
</table>
</div>

