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
	Uses:<br>
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
		<a href="https://github.com/GaryA/antlog/issues">GitHub issue tracker</a>
	</p>

</div>
