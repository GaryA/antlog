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
     * This command echoes what you have entered as the message.
     * @param integer $id Event ID.
     * @param integer $numEntrants Number of entrants.
     */
    public function actionSetup($id, $numEntrants)
    {
    	$event = Event::findOne($id);
		$teams = $event->getTeams($id);
		$event->setupEvent($id, $teams, $numEntrants);
    }
}
