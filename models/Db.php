<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
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
	private $exportFilename;
	private $exportFile;

	public function __construct()
	{
	    $this->username = Yii::$app->db->username;
	    $this->password = Yii::$app->db->password;
	    preg_match('/dbname=(.+)/', Yii::$app->db->dsn, $matches);
	    $this->database = $matches[1];
	    $this->prefix = Yii::$app->db->tablePrefix;
	    $this->exportFilename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $this->database . '_' . date("Y-m-d-H-i-s") . '.sql';

/*
	    $tables =
	    	$this->prefix . 'user' . ' ' .
	    	$this->prefix . 'robot' . ' ' .
	    	$this->prefix . 'event' . ' ' .
	    	$this->prefix . 'entrant' . ' ' .
	    	$this->prefix . 'fights';

	    if ($this->password == '')
	    {
	    	$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $this->username . ' ' . $this->database . ' ' . $tables . ' > ' . $this->filename;
	    }
	    else
	    {
	    	$cmd = 'c:\xampp\mysql\bin\mysqldump.exe -u ' . $this->username . ' -p' . $this->password . ' ' . $this->database . ' ' . $tables . ' > ' . $this->filename;
	    }
	    if (PHP_OS == 'WINNT' || PHP_OS == 'WIN32')
	    {
	    	pclose(popen('start /b ' . $cmd, 'r'));
	    }
	    else
	    {
	    	pclose(popen($cmd, 'r'));
	    }
*/
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
		$this->exportFile = fopen($this->exportFilename, 'a');
		$model = new User;
		$query = $model->find();
		if (ANTLOG_ENV == 'local')
		{
			$query = $query->where(['user_group' => User::ROLE_TEAM]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		$passwordHash = Yii::$app->security->generatePasswordHash('password');
		$userPassword = "`password_hash`='$passwordHash', "; /* dummy password_hash */
		$email = "`email`='email@example.com', "; /* dummy email */
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "user`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "user` (\n");
			fwrite($this->exportFile, " `id` int(11) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->exportFile, " `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->exportFile, " `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->exportFile, " `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->exportFile, " `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,\n");
			fwrite($this->exportFile, " `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->exportFile, " `status` smallint(6) NOT NULL DEFAULT '10',\n");
			fwrite($this->exportFile, " `created_at` int(11) NOT NULL,\n");
			fwrite($this->exportFile, " `updated_at` int(11) NOT NULL,\n");
			fwrite($this->exportFile, " `user_group` smallint(6) NOT NULL DEFAULT '2',\n");
			fwrite($this->exportFile, " `team_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,\n");
			fwrite($this->exportFile, " PRIMARY KEY (`id`),\n");
			fwrite($this->exportFile, " UNIQUE KEY `username` (`username`)\n");
			fwrite($this->exportFile, ") ENGINE=InnoDB  DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (ANTLOG_ENV == 'web')
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
					$email = "`email`='$record->email', "; /* real email */
				}
				else
				{
					$insertRecord = false;
				}
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`username`='$record->username', ";
				if ($record->email != 'email@example.com')
				{
					$update .= "`email`='$record->email', ";
				}
				$update .= "`team_name`='$record->team_name'";
			}

			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "user` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`username`='$record->username', ";
				$string .= $password;
				$string .= "`auth_key`='$record->auth_key', "; /* used for "remember me" */
				$string .= "`password_reset_token`=NULL, "; /* null password_reset_token */
				$string .= $email;
				$string .= "`status`=$record->status, ";
				$string .= $createdAt;
				$string .= $updatedAt;
				$string .= "`user_group`=$record->user_group, ";
				$string .= "`team_name`='$record->team_name'";
				$string .= $update . ";\n";
				fwrite($this->exportFile, $string);
			}
		}
		fclose($this->exportFile);
	}

	public function exportRobots()
	{
		// For web:
		// Export all robots
		// For local:
		// Export all robots (created/modified since last import?)
		$this->exportFile = fopen($this->exportFilename, 'a');
		$model = new Robot;
		$query = $model->find();
		if (ANTLOG_ENV == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "robot`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "robot` (\n");
			fwrite($this->exportFile, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->exportFile, " `name` varchar(100) NOT NULL,\n");
			fwrite($this->exportFile, " `teamId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (teamId) REFERENCES aws_user(id)',\n");
			fwrite($this->exportFile, " `classId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',\n");
			fwrite($this->exportFile, " `typeId` smallint(6) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (typeId) REFERENCES aws_robot_type(id)',\n");
			fwrite($this->exportFile, " `active` tinyint(1) NOT NULL,\n");
			fwrite($this->exportFile, " PRIMARY KEY (`id`),\n");
			fwrite($this->exportFile, " UNIQUE KEY `RobotID_2` (`id`),\n");
			fwrite($this->exportFile, " KEY `RobotID` (`id`)\n");
			fwrite($this->exportFile, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (ANTLOG_ENV == 'web')
			{
				$update = '';
			}
			else
			{
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`name`='$record->name', ";
				$update .= "`classId`=$record->classId, ";
				$update .= "`typeId`=$record->typeId, ";
				$update .= "`active`=$record->active";
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "robot` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`name`='$record->name', ";
				$string .= "`teamId`=$record->teamId, ";
				$string .= "`classId`=$record->classId, ";
				$string .= "`typeId`=$record->typeId, ";
				$string .= "`active`=$record->active";
				$string .= $update . ";\n";
				fwrite($this->exportFile, $string);
			}
		}
		fclose($this->exportFile);
	}

	public function exportEvents()
	{
		// For web:
		// Export all events - to prevent clashes of event IDs for newly-created events?
		// Is it necessary to export events? Or just set the initial auto-increment value?
		// For local:
		// Export all events (created since last import?)
		$this->exportFile = fopen($this->exportFilename, 'a');
		$model = new Event;
		$query = $model->find();
		if (ANTLOG_ENV == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "event`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "event` (\n");
			fwrite($this->exportFile, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->exportFile, " `name` varchar(100) NOT NULL,\n");
			fwrite($this->exportFile, " `eventDate` date NOT NULL,\n");
			fwrite($this->exportFile, " `state` enum('Complete','Running','Setup','Registration','Future') NOT NULL DEFAULT 'Registration',\n");
			fwrite($this->exportFile, " `classId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (classId) REFERENCES aws_robot_class(id)',\n");
			fwrite($this->exportFile, " `eventType` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'CONSTRAINT FOREIGN KEY (eventType) REFERENCES aws_event_type(id)',\n");
			fwrite($this->exportFile, " `num_groups` tinyint(4) NOT NULL DEFAULT '0',\n");
			fwrite($this->exportFile, " `offset` int(11) DEFAULT NULL,\n");
			fwrite($this->exportFile, " PRIMARY KEY (`id`),\n");
			fwrite($this->exportFile, " UNIQUE KEY `id` (`id`)\n");
			fwrite($this->exportFile, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (ANTLOG_ENV == 'web')
			{
				$update = '';
			}
			else
			{
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`state`='$record->state', ";
				$update .= "`num_groups`=$record->num_groups, ";
				$update .= "`offset`=$record->offset";
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "event` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`name`='$record->name', ";
				$string .= "`eventDate`=$record->eventDate, ";
				$string .= "`state`='$record->state', ";
				$string .= "`classId`=$record->classId, ";
				$string .= "`eventType`=$record->eventType, ";
				$string .= "`num_groups`=$record->num_groups, ";
				$string .= "`offset`=$record->offset";
				$string .= $update . ";\n";
				fwrite($this->exportFile, $string);
			}
		}
		fclose($this->exportFile);
	}

	public function exportEntrants()
	{
		// For web:
		// Export all entrants - to cater for online sign-ups
		// For local:
		// Export all entrants (created since last import?)
		$this->exportFile = fopen($this->exportFilename, 'a');
		$model = new Entrant;
		$query = $model->find();
		if (ANTLOG_ENV == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "entrant`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "entrant` (\n");
			fwrite($this->exportFile, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->exportFile, " `eventId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',\n");
			fwrite($this->exportFile, " `robotId` int(10) unsigned NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (robotId) REFERENCES aws_robot(id)',\n");
			fwrite($this->exportFile, " `status` int(11) DEFAULT '-1',\n");
			fwrite($this->exportFile, " `finalFightId` int(11) NOT NULL DEFAULT '0' COMMENT 'CONSTRAINT FOREIGN KEY (finalFightId) REFERENCES aws_fights(id)',\n");
			fwrite($this->exportFile, " `group_num` int(11) DEFAULT NULL,\n");
			fwrite($this->exportFile, " PRIMARY KEY (`id`),\n");
			fwrite($this->exportFile, " UNIQUE KEY `EntrantID` (`id`)\n");
			fwrite($this->exportFile, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (ANTLOG_ENV == 'web')
			{
				$update = '';
			}
			else
			{
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`status`=$record->status, ";
				$update .= "`finalFightId`=$record->finalFightId, ";
				$update .= "`group_num`=$record->group_num";
			}
			if ($insertRecord == true)
			{
				$string = "INSERT INTO `$this->prefix" . "entrant` SET ";
				$string .= "`id`=$record->id, ";
				$string .= "`eventId`=$record->eventId, ";
				$string .= "`robotId`=$record->robotId, ";
				$string .= "`status`=$record->status, ";
				$string .= "`finalFightId`=$record->finalFightId, ";
				$string .= "`group_num`=$record->group_num";
				$string .= $update . ";\n";
				fwrite($this->exportFile, $string);
			}
		}
		fclose($this->exportFile);
	}

	public function exportFights()
	{
		// For web:
		// Export all fights - to prevent changes to robots that are in previous results
		// For local:
		// Export all fights (created since last import?)
		$this->exportFile = fopen($this->exportFilename, 'a');
		$model = new Fights;
		$query = $model->find();
		if (ANTLOG_ENV == 'local')
		{
			//$query = $query->where(['...' => ...]);
		}
	    $records = $query->all();
		$numRecords = $query->count();
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "fights`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `$this->prefix" . "fights` (\n");
			fwrite($this->exportFile, " `id` int(10) NOT NULL AUTO_INCREMENT,\n");
			fwrite($this->exportFile, " `eventId` int(11) NOT NULL COMMENT 'CONSTRAINT FOREIGN KEY (eventId) REFERENCES aws_event(id)',\n");
			fwrite($this->exportFile, " `fightGroup` int(11) NOT NULL,\n");
			fwrite($this->exportFile, " `fightRound` int(11) NOT NULL,\n");
			fwrite($this->exportFile, " `fightBracket` set('W','L','F') NOT NULL,\n");
			fwrite($this->exportFile, " `fightNo` int(11) NOT NULL,\n");
			fwrite($this->exportFile, " `robot1Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot1Id) REFERENCES aws_entrant(id)',\n");
			fwrite($this->exportFile, " `robot2Id` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (robot2Id) REFERENCES aws_entrant(id)',\n");
			fwrite($this->exportFile, " `winnerId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (winnerId) REFERENCES aws_entrant(id)',\n");
			fwrite($this->exportFile, " `loserId` int(11) NOT NULL DEFAULT '-1' COMMENT 'CONSTRAINT FOREIGN KEY (loserId) REFERENCES aws_entrant(id)',\n");
			fwrite($this->exportFile, " `winnerNextFight` int(10) unsigned NOT NULL,");
			fwrite($this->exportFile, " `loserNextFight` int(10) unsigned NOT NULL,");
			fwrite($this->exportFile, " `sequence` int(11) NOT NULL DEFAULT '-1',");
			fwrite($this->exportFile, " PRIMARY KEY (`id`),\n");
			fwrite($this->exportFile, " UNIQUE KEY `FightID` (`id`)\n");
			fwrite($this->exportFile, ") ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;\n");
		}
		foreach ($records as $record)
		{
			$insertRecord = true;
			if (ANTLOG_ENV == 'web')
			{
				$update = '';
			}
			else
			{
				$update = " ON DUPLICATE KEY UPDATE ";
				$update .= "`robot1Id`=$record->robot1Id, ";
				$update .= "`robot2Id`=$record->robot2Id, ";
				$update .= "`winnerId`=$record->winnerId, ";
				$update .= "`loserId`=$record->loserId, ";
				$update .= "`sequence`=$record->sequence";
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
				$string .= "`sequence`=$record->sequence";
				$string .= $update . ";\n";
				fwrite($this->exportFile, $string);
			}
		}
		fclose($this->exportFile);
	}

	public function importUsers()
	{
		// For web:
		// If user id exists, update username if different, update team_name if different, update email if not dummy value
		// If user id does not exist, create user
		// For local:
		// Drop existing user table, create new user table from import data

	}
	public function importRobots()
	{
		// For web:
		// If robot id exists, update name, team, status etc if allowable - should always be OK
		// If robot id does not exist, create robot
		// For local:
		// Drop existing robot table, create new robot table from import data

	}

	public function importEvents()
	{
		// For web:
		// If event id exists, update state, num_groups, offset
		// If event id does not exist, create event (copy from import data, don't create as new event)
		// For local:
		// Drop existing event table, create new event table from import data

	}

	public function importEntrants()
	{
		// For web:
		// If entrant id exists, ignore
		// If entrant id does not exist, create entrant
		// For local:
		// Drop existing entrant table, create new entrant table from import data

	}

	public function importFights()
	{
		// For web:
		// If fight id exists, ignore
		// If fight id does not exist, create fight
		// For local:
		// Drop existing fights table, create new fights table from import data

	}
}