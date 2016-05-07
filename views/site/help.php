<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Help';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-help">
<h1>
<?php
echo(Html::encode($this->title));
if ($user == 'admin')
{
	echo(' for Administrators');
}
else
{
	echo(' for Teams');
}
?>
</h1>
<?php
if ($user == 'admin')
{
	if ($env == 'local')
	{
		echo $this->render('_admin_local', []);
	}
}
else
{
	if ($env == 'local')
	{
		echo $this->render('_team_local', []);
	}
	else
	{
		echo $this->render('_team_web', []);
	}
}
?>
</div>