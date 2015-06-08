<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Event;
use Yii;

/**
 * This controller runs long processes so that a progress bar can be displayed to the user.
 */
class EventController extends Controller
{
    /**
     * This command runs the event setup action.
     * @param integer $id Event ID.
     * @param integer $numEntrants Number of entrants.
     */
    public function actionSetup($postId, $eventId, $numEntrants)
    {
    	set_time_limit(0);
		$filename = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . "{$postId}";
		$post = file_get_contents($filename);
		$post = json_decode($post, true);
		$key = $post['progress_key'];

    	$event = Event::findOne($eventId);
		$teams = $event->getTeams($eventId);
		$event->setupEvent($key, $eventId, $teams, $numEntrants);

		unlink($filename);
    }
}
