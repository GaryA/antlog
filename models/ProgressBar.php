<?php

namespace app\controllers;

/**
 * ProgressBar class uses Yii cache to store progress data and provides functions to get and set data
 * (From Yii tutorial by hehbhehb)
 *
 * @property integer $key
 * @property boolean $running
 * @property integer $total
 * @property integer $done
 *
 */
class ProgressBar
{
	private $key;

	private $running;
	private $totalAmount;
	private $amountComplete;

	public function __construct($key)
	{
		$this->key = $key;
	}

	public function start($total)
	{
		$this->running = 1;
		$this->amountComplete = 0;
		$this->totalAmount = $total;
		$this->put();
	}

	public function stop()
	{
		$this->running = 0;
		$this->put();
	}

	public function inc($step=1)
	{
		$this->amountComplete += $step;
		$this->put();
	}

	public function put()
	{
		$ret = Yii::app()->cache->set($this->key, ['running'=>$this->running, 'totalAmount'=>$this->totalAmount, 'amountComplete'=>$this->amountComplete]);
	}

	public static function get($key)
	{
		$test = 1;
		//$test = 0;
		if ($test)
		{
			$data = Yii::app()->cache->get($key);
			if($data === false)
			{
				$data = ['running'=>1, 'totalAmount'=>119, 'amountComplete'=>0];
			}

			$data['amountComplete'] = $data['amountComplete'] + 10;

			if ($data['amountComplete'] > 119)
			{
				$data['running'] = 0;
			}

			Yii::app()->cache->set($key, $data, 1*60);
			return $data;
		}

		$data = Yii::app()->cache->get($key);
		if($data === false)
		{
			$data = ['running' =>1, 'totalAmount'=>100, 'amountComplete'=>0];
		}
		return $data;
	}
}