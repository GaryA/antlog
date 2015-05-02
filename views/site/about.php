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
		<a href="https://github.com/GaryA/antlog/issues">GitHub issue tracker</a>
	</p>
</div>
