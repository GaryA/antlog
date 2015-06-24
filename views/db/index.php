<?php
use yii\helpers\Html;
use app\models\User;

$this->title = 'Database';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="database-index">

    <h1><?= Html::encode($this->title . ' - ' . $mode) ?></h1>
    <?php
    $username = Yii::$app->db->username;
    $password = Yii::$app->db->password;
    preg_match('/dbname=(.+)/', Yii::$app->db->dsn, $matches);
    $database = $matches[1];
    $prefix = Yii::$app->db->tablePrefix;
    $tables =
    	$prefix . 'user' . ' ' .
    	$prefix . 'robot' . ' ' .
    	$prefix . 'event' . ' ' .
    	$prefix . 'entrant' . ' ' .
    	$prefix . 'fights';
    if ($mode == 'Export')
    {
    	$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $database . '_' . date("Y-m-d-H-i-s") . '.sql';
    	if ($password == '')
    	{
    		$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $username . ' ' . $database . ' ' . $tables . ' > ' . $filename;
    	}
    	else
    	{
    		$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $username . ' -p' . $password . ' ' . $database . ' ' . $tables . ' > ' . $filename;
    	}
    	if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32')
    	{
    		pclose(popen('start /b ' . $cmd, 'r'));
    	}
    	else
    	{
    		pclose(popen($cmd, 'r'));
    	}
    	echo 'User, Robot, Event, Entrant and Fights tables dumped to ' . $filename;
    }
    else if ($mode == 'Import')
    {
    	echo 'Import of data is not yet implemented!';
    }
    ?>

</div>
