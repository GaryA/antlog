<?php
use yii\helpers\Html;

$this->title = 'Export Database Updates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
<h1><?= Html::encode($this->title) ?></h1>
<p>
The download should start automatically. If it doesn't,
<?php
echo(Html::a('click here', '/db/export'));
?>
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
	echo("Save the file and import it into your local copy of Antlog.");
}
?>
</p>
</div>

<script type="text/javascript" language="JavaScript"><!--
// This should be a in a javascript file loaded using the Yii registerJsFile method...
function Redirect()
{
	// Redirect to a new URL, since the redirects to an SQL file it just causes a file
	// download, not a visible page redirect. The user appears to stay on the original page
	window.location = "/db/export";
}

// Automatically call the redirect some time after this page loads (100 ms works OK)
window.onload = function()
{
	setTimeout("Redirect()", 100);
}

//--></script>
