<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\QueryBuilder;
use app\models\Robot;
use app\models\User;
use app\models\Event;
use app\models\Entrant;
use app\models\Fights;

/**
 * Db model
 *
 * Provides import/export methods for database tables
 *
 */
class Db extends ActiveRecord
{
	/* Parameters to access the database */
	private $username;
	private $password;
	private $database;
	private $prefix;
	private $filename;
	private $fileHandle;
	private $queryBuilder;

	public function __construct()
	{
	    $this->queryBuilder = new QueryBuilder(Yii::$app->db);
		$this->username = Yii::$app->db->username;
	    $this->password = Yii::$app->db->password;
	    preg_match('/dbname=(.+)/', Yii::$app->db->dsn, $matches);
	    $this->database = $matches[1];
	    $this->prefix = Yii::$app->db->tablePrefix;
	    $this->filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $this->database . '_' . date("Y-m-d-H-i-s") . '.sql';
	}

	public function exportEnd()
	{
		$this->fileHandle = fopen($this->filename, 'a');
		fwrite($this->fileHandle, "SELECT '<COMPLETE>' AS ' ';\n");
		fclose($this->fileHandle);
	}

	public function exportUsers()
	{
		// For web:
		// Assume the local installation contains empty tables so export everything, but use dummy email & password
		// Export dummy email addresses and password as "password" for all teams
		// Export dummy email address and password as "admin" for administrator
	    // For local:
		// Export only items that have been created/modified since the last import? This would save unnecessary data
		// Export all teams, do not export administrator (created/modified since last import?)
		$this->fileHandle = fopen($this->filename, 'a');
		$model = new User;
		$query = $model->find();
		if (Yii::$app->params['antlog_env'] == 'local')
		{
			$query = $query->where(['user_group' => User::ROLE_TEAM]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		$passwordHash = Yii::$app->security->generatePasswordHash('password');
		$userPassword = "`password_hash`='$passwordHash', "; /* dummy password_hash */
		$email = "`email`='email@example.com', "; /* dummy email */
		if (Yii::$app->params['antlog_env'] == 'web')
		{
			fwrite($this->fileHandle, "DROP TABLE IF EXISTS `$this->prefix" . "user`;\n");
			fwrite($this->fileHandle, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "user` (\n");
			fwrite($this->fileHandle, " `id` int(11) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->fileHandle, " `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->fileHandle, " `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->fileHandle, " `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->fileHandle, " `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,\n");
			fwrite($this->fileHandle, " `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->fileHandle, " `status` smallint(6) NOT NULL DEFAULT '10',\n");
			fwrite($this->fileHandle, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `user_group` smallint(6) NOT NULL DEFAULT '2',\n");
			fwrite($this->fileHandle, " `team_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->fileHandle, " PRIMARY KEY (`id`),\n");
			fwrite($this->fileHandle, " UNIQUE KEY `username` (`username`)\n");
			fwrite($this->fileHandle, ") ENGINE=InnoDB  DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (Yii::$app->params['antlog_env'] == 'web')
			{
				// INSERT INTO table SET col1=val1, col2=val2;
				if ($record->user_group == User::ROLE_ADMIN)
				{
					$password = "`password_hash`='" . Yii::$app->security->generatePasswordHash('admin') . "', ";
				}
				else
				{
					$password = $userPassword;
				}
				$createdAt = "`created_at`=0, ";
				$updatedAt = "`updated_at`=0, ";
				$update = '';
			}
			else
			{
				$createdAt = "`created_at`=$record->created_at, ";
				$updatedAt = "`updated_at`=$record->updated_at, ";
				if (($record->created_at > 0) || ($record->updated_at > 0))
				{
					// INSERT INTO table SET col1=val1, col2=val2 ON DUPLICATE KEY UPDATE col1=val1, col2=val2;
					$password = "`password_hash`='$record->password_hash', "; /* export actual password hash */
					$email = "`email`='" . $this->escapeString($record->email) . "', "; /* real email */
				}
				else
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`username`='" . $this->escapeString($record->username) . "', ";
				if ($record->email != 'email@example.com')
				{
					$update .= "`email`='" . $this->escapeString($record->email) . "', ";
				}
				$update .= "`team_name`='" . $this->escapeString($record->team_name) . "'";
				if ($record->created_at != 0)
				{
					$update .= ", `created_at`=$record->created_at";
				}
				if ($record->updated_at != 0)
				{
					$update .= ", `updated_at`=$record->updated_at";
				}
			}

			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "user` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`username`='" . $this->escapeString($record->username) . "', ";
				$string .= $password;
				$string .= "`auth_key`='$record->auth_key', "; /* used for "remember me" */
				$string .= "`password_reset_token`=NULL, "; /* null password_reset_token */
				$string .= $email;
				$string .= "`status`=$record->status, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				$string .= "`user_group`=$record->user_group, ";
				$string .= "`team_name`='" . $this->escapeString($record->team_name) . "'";
				$string .= $update . ";\n";
				fwrite($this->fileHandle, $string);
			}
		}
		fclose($this->fileHandle);
	}

	public function exportRobots()
	{
		// For web:
		// Export all robots
		// For local:
		// Export all robots (created/modified since last import?)
		$this->fileHandle = fopen($this->filename, 'a');
		$model = new Robot;
		$query = $model->find();
		if (Yii::$app->params['antlog_env'] == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (Yii::$app->params['antlog_env'] == 'web')
		{
			fwrite($this->fileHandle, "DROP TABLE IF EXISTS `$this->prefix" . "robot`;\n");
			fwrite($this->fileHandle, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "robot` (\n");
			fwrite($this->fileHandle, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->fileHandle, " `name` varchar(100) NOT NULL,\n");
			fwrite($this->fileHandle, " `teamId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (teamId) REFERENCES aws_user(id)',\n");
			fwrite($this->fileHandle, " `classId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',\n");
			fwrite($this->fileHandle, " `typeId` smallint(6) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (typeId) REFERENCES aws_robot_type(id)',\n");
			fwrite($this->fileHandle, " `active` tinyint(1) NOT NULL,\n");
			fwrite($this->fileHandle, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " PRIMARY KEY (`id`),\n");
			fwrite($this->fileHandle, " UNIQUE KEY `RobotID_2` (`id`),\n");
			fwrite($this->fileHandle, " KEY `RobotID` (`id`)\n");
			fwrite($this->fileHandle, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (Yii::$app->params['antlog_env'] == 'web')
			{
				$createdAt = "`created_at`=0, ";
				$updatedAt = "`updated_at`=0, ";
				$update = '';
			}
			else
			{
				$createdAt = "`created_at`=$record->created_at, ";
				$updatedAt = "`updated_at`=$record->updated_at, ";
				if (($record->created_at == 0) && ($record->updated_at == 0))
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`name`='" . $this->escapeString($record->name) . "', ";
				$update .= "`classId`=$record->classId, ";
				$update .= "`typeId`=$record->typeId, ";
				$update .= "`active`=$record->active";
				if ($record->created_at != 0)
				{
					$update .= ", `created_at`=$record->created_at";
				}
				if ($record->updated_at != 0)
				{
					$update .= ", `updated_at`=$record->updated_at";
				}
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "robot` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`name`='" . $this->escapeString($record->name) . "', ";
				$string .= "`teamId`=$record->teamId, ";
				$string .= "`classId`=$record->classId, ";
				$string .= "`typeId`=$record->typeId, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				$string .= "`active`=$record->active";
				$string .= $update . ";\n";
				fwrite($this->fileHandle, $string);
			}
		}
		fclose($this->fileHandle);
	}

	public function exportEvents()
	{
		// For web:
		// Export all events - to prevent clashes of event IDs for newly-created events?
		// Is it necessary to export events? Or just set the initial auto-increment value?
		// For local:
		// Export all events (created since last import?)
		$this->fileHandle = fopen($this->filename, 'a');
		$model = new Event;
		$query = $model->find();
		if (Yii::$app->params['antlog_env'] == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (Yii::$app->params['antlog_env'] == 'web')
		{
			fwrite($this->fileHandle, "DROP TABLE IF EXISTS `$this->prefix" . "event`;\n");
			fwrite($this->fileHandle, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "event` (\n");
			fwrite($this->fileHandle, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->fileHandle, " `name` varchar(100) NOT NULL,\n");
			fwrite($this->fileHandle, " `eventDate` date NOT NULL,\n");
			fwrite($this->fileHandle, " `state` enum('Complete','Running','Ready','Setup','Registration','Future') NOT NULL DEFAULT 'Registration',\n");
			fwrite($this->fileHandle, " `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',\n");
			fwrite($this->fileHandle, " `eventType` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'CONSTRAINT FOREIGN KEY (eventType) REFERENCES aws_event_type(id)',\n");
			fwrite($this->fileHandle, " `num_groups` tinyint(4) NOT NULL DEFAULT '0',\n");
			fwrite($this->fileHandle, " `offset` int(11) DEFAULT NULL,\n");
			fwrite($this->fileHandle, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `organiserId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (organiserId) REFERENCES aws_user(id)',\n");
			fwrite($this->fileHandle, " `venue` text NOT NULL,\n");
			fwrite($this->fileHandle, " PRIMARY KEY (`id`),\n");
			fwrite($this->fileHandle, " UNIQUE KEY `id` (`id`)\n");
			fwrite($this->fileHandle, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (Yii::$app->params['antlog_env'] == 'web')
			{
				$createdAt = "`created_at`=0, ";
				$updatedAt = "`updated_at`=0, ";
				$update = '';
			}
			else
			{
				$createdAt = "`created_at`=$record->created_at, ";
				$updatedAt = "`updated_at`=$record->updated_at, ";
				if (($record->created_at == 0) && ($record->updated_at == 0))
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`name`='" . $this->escapeString($record->name) . "', ";
				$update .= "`eventDate`='$record->eventDate', ";
				$update .= "`state`='$record->state', ";
				$update .= "`eventType`=$record->eventType, ";
				$update .= "`num_groups`=$record->num_groups, ";
				if ($record->offset == NULL)
				{
					$update .= "`offset`=NULL";
				}
				else
				{
					$update .= "`offset`=$record->offset";
				}
				if ($record->created_at != 0)
				{
					$update .= ", `created_at`=$record->created_at";
				}
				if ($record->updated_at != 0)
				{
					$update .= ", `updated_at`=$record->updated_at";
				}
				$update .= ", `organiserId`=$record->organiserId";
				$update .= ", `venue`='" . $this->escapeString($record->venue) . "'";
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "event` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`name`='" . $this->escapeString($record->name) . "', ";
				$string .= "`eventDate`='$record->eventDate', ";
				$string .= "`state`='$record->state', ";
				$string .= "`classId`=$record->classId, ";
				$string .= "`eventType`=$record->eventType, ";
				$string .= "`num_groups`=$record->num_groups, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				$string .= "`organiserId`=$record->organiserId, ";
				$string .= "`venue`='" . $this->escapeString($record->venue) . "', ";
				if ($record->offset == NULL)
				{
					$string .= "`offset`=NULL";
				}
				else
				{
					$string .= "`offset`=$record->offset";
				}
				$string .= $update . ";\n";
				fwrite($this->fileHandle, $string);
			}
		}
		fclose($this->fileHandle);
	}

	public function exportEntrants()
	{
		// For web:
		// Export all entrants - to cater for online sign-ups
		// For local:
		// Export all entrants (created since last import?)
		$this->fileHandle = fopen($this->filename, 'a');
		$model = new Entrant;
		$query = $model->find();
		if (Yii::$app->params['antlog_env'] == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (Yii::$app->params['antlog_env'] == 'web')
		{
			fwrite($this->fileHandle, "DROP TABLE IF EXISTS `$this->prefix" . "entrant`;\n");
			fwrite($this->fileHandle, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "entrant` (\n");
			fwrite($this->fileHandle, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->fileHandle, " `eventId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',\n");
			fwrite($this->fileHandle, " `robotId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (robotId) REFERENCES aws_robot(id)',\n");
			fwrite($this->fileHandle, " `status` int(11) DEFAULT '-1',\n");
			fwrite($this->fileHandle, " `finalFightId` int(11) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (finalFightId) REFERENCES aws_fights(id)',\n");
			fwrite($this->fileHandle, " `group_num` int(11) DEFAULT NULL,\n");
			fwrite($this->fileHandle, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " PRIMARY KEY (`id`),\n");
			fwrite($this->fileHandle, " UNIQUE KEY `EntrantID` (`id`)\n");
			fwrite($this->fileHandle, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (Yii::$app->params['antlog_env'] == 'web')
			{
				$createdAt = "`created_at`=0, ";
				$updatedAt = "`updated_at`=0, ";
				$update = '';
			}
			else
			{
				$createdAt = "`created_at`=$record->created_at, ";
				$updatedAt = "`updated_at`=$record->updated_at, ";
				if (($record->created_at == 0) && ($record->updated_at == 0))
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`status`=$record->status, ";
				$update .= "`finalFightId`=$record->finalFightId, ";
				if ($record->group_num == NULL)
				{
					$update .= "`group_num`=NULL";
				}
				else
				{
					$update .= "`group_num`=$record->group_num";
				}
				if ($record->created_at != 0)
				{
					$update .= ", `created_at`=$record->created_at";
				}
				if ($record->updated_at != 0)
				{
					$update .= ", `updated_at`=$record->updated_at";
				}
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "entrant` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`eventId`=$record->eventId, ";
				$string .= "`robotId`=$record->robotId, ";
				$string .= "`status`=$record->status, ";
				$string .= "`finalFightId`=$record->finalFightId, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				if ($record->group_num == NULL)
				{
					$string .= "`group_num`=NULL";
				}
				else
				{
					$string .= "`group_num`=$record->group_num";
				}
				$string .= $update . ";\n";
				fwrite($this->fileHandle, $string);
			}
		}
		fclose($this->fileHandle);
	}

	public function exportFights()
	{
		// For web:
		// Export all fights - to prevent changes to robots that are in previous results
		// For local:
		// Export all fights (created since last import?)
		$this->fileHandle = fopen($this->filename, 'a');
		$model = new Fights;
		$query = $model->find();
		if (Yii::$app->params['antlog_env'] == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (Yii::$app->params['antlog_env'] == 'web')
		{
			fwrite($this->fileHandle, "DROP TABLE IF EXISTS `$this->prefix" . "fights`;\n");
			fwrite($this->fileHandle, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "fights` (\n");
			fwrite($this->fileHandle, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->fileHandle, " `eventId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',\n");
			fwrite($this->fileHandle, " `fightGroup` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `fightRound` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `fightBracket` set('W','L','F') NOT NULL,\n");
			fwrite($this->fileHandle, " `fightNo` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `robot1Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot1Id) REFERENCES aws_entrant(id)',\n");
			fwrite($this->fileHandle, " `robot2Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot2Id) REFERENCES aws_entrant(id)',\n");
			fwrite($this->fileHandle, " `winnerId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (winnerId) REFERENCES aws_entrant(id)',\n");
			fwrite($this->fileHandle, " `loserId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (loserId) REFERENCES aws_entrant(id)',\n");
			fwrite($this->fileHandle, " `winnerNextFight` int(10) unsigned NOT NULL,");
			fwrite($this->fileHandle, " `loserNextFight` int(10) unsigned NOT NULL,");
			fwrite($this->fileHandle, " `sequence` int(11) NOT NULL DEFAULT '-1',");
			fwrite($this->fileHandle, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->fileHandle, " PRIMARY KEY (`id`),\n");
			fwrite($this->fileHandle, " UNIQUE KEY `FightID` (`id`)\n");
			fwrite($this->fileHandle, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (Yii::$app->params['antlog_env'] == 'web')
			{
				$createdAt = "`created_at`=0, ";
				$updatedAt = "`updated_at`=0, ";
				$update = '';
			}
			else
			{
				$createdAt = "`created_at`=$record->created_at, ";
				$updatedAt = "`updated_at`=$record->updated_at, ";
				if (($record->created_at == 0) && ($record->updated_at == 0))
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`robot1Id`=$record->robot1Id, ";
				$update .= "`robot2Id`=$record->robot2Id, ";
				$update .= "`winnerId`=$record->winnerId, ";
				$update .= "`loserId`=$record->loserId, ";
				$update .= "`sequence`=$record->sequence";
				if ($record->created_at != 0)
				{
					$update .= ", `created_at`=$record->created_at";
				}
				if ($record->updated_at != 0)
				{
					$update .= ", `updated_at`=$record->updated_at";
				}
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "fights` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`eventId`=$record->eventId, ";
				$string .= "`fightGroup`=$record->fightGroup, ";
				$string .= "`fightRound`=$record->fightRound, ";
				$string .= "`fightBracket`='$record->fightBracket', ";
				$string .= "`fightNo`=$record->fightNo, ";
				$string .= "`robot1Id`=$record->robot1Id, ";
				$string .= "`robot2Id`=$record->robot2Id, ";
				$string .= "`winnerId`=$record->winnerId, ";
				$string .= "`loserId`=$record->loserId, ";
				$string .= "`winnerNextFight`=$record->winnerNextFight, ";
				$string .= "`loserNextFight`=$record->loserNextFight, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				$string .= "`sequence`=$record->sequence";
				$string .= $update . ";\n";
				fwrite($this->fileHandle, $string);
			}
		}
		fclose($this->fileHandle);
	}

	public function importFile($fileName)
	{
		// run mysql with $fileName as input
		if ($this->password !== '')
		{
			$cmd = "-h localhost -u $this->username -p $this->password $this->database < \"$fileName\"";
		}
		else
		{
			$cmd = "-h localhost -u $this->username $this->database < \"$fileName\"";
		}
		if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32')
		{
			exec("start /b c:\\xampp\\mysql\\bin\\mysql.exe $cmd");
		}
		else
		{
			pclose(popen('mysql ' . $cmd . '> /dev/null &', 'r'));
		}
		//unlink($fileName);
		// SELECT * FROM `aws_entrant`
		// LEFT JOIN (`aws_event`) ON `eventId` = `aws_event`.`id`
		// WHERE `status` = -1 AND `state` LIKE "Complete"
		$entrants = Entrant::find()
			->joinWith('event')
			->where(['status' => -1])
			->andWhere(['like', 'state', 'Complete'])
			->all();
		foreach ($entrants as $entrant)
		{
			$entrant->delete();
		}
	}

	public function fileDownload()
	{
		$fileSize  = filesize($this->filename);
		$pathInfo = pathinfo($this->filename);
		$fileName = $pathInfo['basename'];
		$file = @fopen($this->filename,"rb");
		if ($file)
		{
			header("Pragma: public");
			header("Expires: -1");
			header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
			header("Content-Disposition: attachment; filename=\"$fileName\"");
			header("Content-Type: application/octet-stream");
			header("Content-Length: $fileSize");
			set_time_limit(0);
			while(!feof($file))
			{
				print(@fread($file, 1024*8));
				ob_flush();
				flush();
				if (connection_status()!=0)
				{
					@fclose($file);
					exit;
				}
			}
			@fclose($file);
			return;
		}
		else
		{
			header("HTTP/1.0 500 Internal Server Error");
			exit;
		}
	}

	/**
	 * Function to escape string so that is can be used in SQL query
	 * @param string $string
	 * @return string
	 */
	private function escapeString($string)
	{
		$retVal = str_replace(["'", '\\'], ["''", '\\\\'], $string);
		return $retVal;
	}
	private function validateQuery($query)
	{
		// Users:
		// Check that users are not administrators before processing insert/update
		// Robots:
		// check that team exists before processing insert/update
		// Events:
		// If event id exists, update state, num_groups, offset
		// If event id does not exist, create event (copy from import data, don't create as new event)
		// Entrants:
		// If entrant id exists, ignore
		// If entrant id does not exist, create entrant
		// Fights:
		// If fight id exists, ignore
		// If fight id does not exist, create fight

	}
}