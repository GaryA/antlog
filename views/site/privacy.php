<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Privacy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-help">
<h1>
<?php
echo(Html::encode($this->title));
?>
</h1>
<?php
echo $this->render('_privacy_statement', []);
?>
</div>
