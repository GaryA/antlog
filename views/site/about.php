<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?> AntLog 3.1</h1>
    <h2>Version <?= Yii::$app->version ?></h2>
    <p>
        AntLog 3.1 is a web application written in PHP using the Yii framework. It runs on any
		LAMP/WAMP/MAMP (Linux/Windows/Mac - Apache - MySQL - PHP) server and can be accessed via any
		web browser.
    </p>
	<p>
	AntLog is built on, and owes much to:<br>
	<ul>
	<li><a href="http://www.yiiframework.com/">Yii 2 framework</a>
	</li>
	<li>yii2-grid-view library from <a href="https://github.com/2amigos/yii2-grid-view-library/blob/master/GroupGridView.php">
	2amigos</a>
	</li>
	<li>yii2-widget-datepicker from <a href="https://github.com/kartik-v/yii2-widget-datepicker">kartik-v</a>
	</li>
	<li>Console-runner extension from <a href="https://github.com/vova07/yii2-console-runner-extension">vova07</a>
	</li>
	</ul>
	</p>
	<p>
	Please report any bugs via the <a href="https://github.com/GaryA/antlog/issues">GitHub issue tracker</a>
	or the <a href="http://robotwars101.org/forum/viewtopic.php?f=9&t=1250&start=45">RobotWars101 forum</a>
	</p>

</div>
