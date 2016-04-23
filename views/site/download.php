<?php
use yii\helpers\Html;

$this->title = 'Export Database Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h1><?= Html::encode($this->title) ?></h1>
<p>
The download should start automatically. If it doesn't,
<?= (Html::a('click here', '/db/export')) ?>
 to download manually.
</p>
<p>
<?php
if (Yii::$app->params['antlog_env'] == 'local')
{
	$addr = 'antlog@garya.org.uk';
	$subject = 'Antlog%20Database%20Update';
	$body = 'Don%27t%20forget%20to%20attach%20the%20update%20file%21%0A%0A';
	echo("Save the file and email it to ");
	echo(Html::mailto($addr, "$addr?subject=$subject&body=$body"));
}
else
{
	echo("Save the file and import it into your local copy of Antlog.<br><br>");
	echo("Online registration for your events is now closed.<br>");
	echo("Registration at the event is still possible.<br>");
	echo("To re-open online registration, go to the event page(s).");
}
?>
</p>
</div>
<?php
$this->registerJsFile(
	Yii::getAlias('@web') . '/js/download.js',
	['depends' => 'yii\web\YiiAsset'],
	__CLASS__);
?>

