<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\Robot;

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
		$password = "`password_hash`='$passwordHash', "; /* dummy password_hash */
		$email = "`email`='email@example.com', "; /* dummy email */
		if (ANTLOG_ENV == 'web')
		{
			fwrite($this->exportFile, "DROP TABLE IF EXISTS `$this->prefix" . "user`;\n");
			fwrite($this->exportFile, "CREATE TABLE IF NOT EXISTS `aws_user` (\n");
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
	}

	public function exportEvents()
	{
		// For web:
		// Export all events - to prevent clashes of event IDs for newly-created events?
		// Is it necessary to export events? Or just set the initial auto-increment value?
		// For local:
		// Export all events (created since last import?)
	}

	public function exportEntrants()
	{
		// For web:
		// Export all entrants - to cater for online sign-ups
		// For local:
		// Export all entrants (created since last import?)
	}

	public function exportFights()
	{
		// For web:
		// Export all fights - to prevent changes to robots that are in previous results
		// For local:
		// Export all fights (created since last import?)
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
		// If event id exists, ignore
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