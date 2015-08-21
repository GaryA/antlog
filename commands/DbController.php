<?php
namespace app\commands;

use yii\console\Controller;
use app\models\ProgressBar;
use app\models\Db;
use Yii;

/**
 * This controller runs long processes so that a progress bar can be displayed to the user.
 */
class DbController extends Controller
{
	/**
	 * This command runs the event run action
	 * @param string $postId ID of post data
	 * @param string $redirect Page to redirect to on completion
	 */
	public function actionImport($postId, $importfile, $redirect = '')
	{
    	set_time_limit(0);
    	$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
    	$key = $this->getProgressKey($filename);

		$progress = new ProgressBar($key);
		$progress->start(100, $redirect);
		$progress->set(99);

		$db = new Db;
		$db->importFile($importfile);

		$progress->complete();
		unlink($filename);
	}

	/**
	 * Get key to progress file/cache from post data
	 * @param string $filename Name of post data file
	 * @return string Progress key
	 */
	protected function getProgressKey($filename)
	{
		$post = file_get_contents($filename);
		$post = json_decode($post, true);
		return $post['progress_key'];
	}
}