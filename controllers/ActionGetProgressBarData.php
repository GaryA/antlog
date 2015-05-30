<?php
use app\controllers\ProgressBar;

/**
 * Action class to get progress bar data
 *
 */
class ActionGetProgressBarData extends Action
{
	public function run($key)
	{
		$response = ProgressBar::get($key);
		echo json_encode($response);
	}

}

?>