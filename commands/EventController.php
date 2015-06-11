<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Event;
use app\models\Fights;
use app\models\ProgressBar;
use Yii;

/**
 * This controller runs long processes so that a progress bar can be displayed to the user.
 */
class EventController extends Controller
{
    /**
     * This command runs the event setup action.
     * @param string $postId ID of post data
     * @param string $redirect Page to redirect to on completion
     * @param integer $id Event ID.
     * @param integer $numEntrants Number of entrants.
     */
    public function actionSetup($postId, $eventId, $numEntrants, $redirect = '')
    {
    	set_time_limit(0);
    	$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
    	$key = $this->getProgressKey($filename);
    	$event = Event::findOne($eventId);
		$teams = $event->getTeams($eventId);
		$event->setupEvent($key, $redirect, $eventId, $teams, $numEntrants);

		unlink($filename);
    }

    /**
     * This command runs the event run action
     * @param string $postId ID of post data
     * @param string $redirect Page to redirect to on completion
     * @param integer $eventId Event ID
     */
    public function actionRun($postId, $eventId, $redirect = '')
    {
    	set_time_limit(0);
    	$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
    	$key = $this->getProgressKey($filename);

    	$fights = new Fights();
		$progress = new ProgressBar($key);
		$progress->start(255, $redirect);
    	$count = 0;
    	do
    	{
    		$status = $fights->runByes($eventId);
    		$count += 1;
    		$progress->inc();
    	} while ($status == true);
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
