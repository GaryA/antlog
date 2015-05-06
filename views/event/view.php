<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
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
								if (User::isUserAdmin())
								{
									echo Html::a('Update', [
										'update',
										'id' => $model->id
									], [
										'class' => 'btn btn-primary'
									]);
									echo Html::a('Delete', [
										'delete',
										'id' => $model->id
									], [
										'class' => 'btn btn-danger',
										'data' => [
											'confirm' => 'Are you sure you want to delete this item?',
											'method' => 'post'
										]
									]);

									if ($model->state == 'Registration')
									{
										echo Html::a('Do Draw', [
											'draw',
											'id' => $model->id
										], [
											'class' => 'btn btn-primary'
										]);
									}
									else
										if ($model->state == 'Setup')
										{
											echo Html::a('Re-Do Draw', [
												'setup',
												'id' => $model->id
											], [
												'class' => 'btn btn-primary'
											]);
										}
										else
											if ($model->state == 'Running')
											{
												echo Html::a('Run Fights', [
													'run',
													'id' => $model->id
												], [
													'class' => 'btn btn-primary'
												]);
											}
								}
								echo Html::a('Entrants', [
									'@web/entrant',
									'eventId' => $model->id
								], [
									'class' => 'btn btn-primary'
								]);
								if (($model->state == 'Running') || ($model->state == 'Complete'))
								{
									echo Html::a('Fights', [
										'@web/fights',
										'eventId' => $model->id
									], [
										'class' => 'btn btn-primary'
									]);
								}
								if ($model->state == 'Complete')
								{
									echo Html::a('Results', [
										'result',
										'id' => $model->id
									], [
										'class' => 'btn btn-primary'
									]);
								}
								?>
    </p>

    <?php
    echo DetailView::widget(
    	[
    		'model' => $model,
    		'attributes' =>
    		[
    			// 'id',
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
		if ($model->robot->type == '')
		{
			echo $model->robot->name . '<br>';
		}
		else
		{
			echo $model->robot->name . ' (' . $model->robot->type . ')<br>';
		}
	}
	echo '</td><td>' . count($robots) . '</td></tr>';
}
?>
</table>
</div>