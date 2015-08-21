<div id="progress-wrapper" style="display:none">
<p>Processing, please wait...</p>
<?= yii\bootstrap\Progress::widget([
	'percent' => 0,
	'barOptions' => ['id' => 'progress-bar', 'class' => 'progress-bar-success'],
	'options' => ['id' => 'progress', 'class' => 'active']
])?>
</div>
