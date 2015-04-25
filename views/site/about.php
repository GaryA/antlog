<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        AntLog 3.0 is a web application written in PHP using the Yii framework. It runs on any
		LAMP/WAMP/MAMP (Linux/Windows/Mac - Apache - MySQL - PHP) server and can be accessed via any
		web browser.
    </p>
	<p>
	Teams
	<ul>
		<li class="text-muted">Teams can be created by anyone ( = user signup)</li>
		<li class="text-muted">Teams can be renamed even if they appear in the fights table</li>
		<li class="text-muted">Renaming a team also changes the associated username</li>
		<li class="text-muted">Teams can only be renamed by the team owner or administrator</li>
		<li class="text-muted">Teams cannot be deleted if they contain any robots</li>
		<li class="text-muted">Teams can only be deleted by the administrator</li>
	</ul>
	</p>
	<p>
	Robots
	<ul>
		<li class="text-muted">Robots cannot be edited if they appear in the fights table</li>
		<li class="text-muted">Robots cannot be deleted if they appear in the fights table</li>
		<li class="text-muted">Robots can only be edited by Admin or own team</li>
		<li class="text-muted">Robots can be added by Admin or own team</li>
		<li>Robots table needs additional column to allow robots to be retired from active duty</li>
	</ul>
	</p>
	<p>
	Events
	<ul>
		<li class="text-muted">Events can only change state through action buttons</li>
		<li class="text-muted">Events cannot be edited if they have any entrants (except below)</li>
		<li class="text-muted">Event name can only be changed in Registration state</li>
		<li class="text-muted">Events cannot be deleted if they have any entrants</li>
		<li class="text-muted">Events can only be created by Admin</li>
	</ul>
	</p>
	<p>
	Entrants
	<ul>
		<li class="text-muted">Entrants can only be added/deleted/edited by Admin</li>
		<li class="text-muted">Entrant robot may be changed only if event is in Registration state</li>
		<li class="text-muted">Entrants may only be added or deleted to events in Registration state</li>
		<li class="text-muted">Entrant event may not be changed</li>
		<li>Robots should be chosen only from weight class matching event - needs Javascript???</li>
	</ul>
	</p>
	<p>
	Fights
	<ul>
		<li>Fights view should show completed fights/results for running/complete events</li>
	</ul>
	</p>
</div>
