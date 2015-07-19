<?php

namespace app\models;

use Yii;

/**
 * ProgressBar class uses Yii cache or file to store progress data and provides functions to get and set data
 * (From Yii tutorial by hehbhehb)
 *
 * @property string $key
 * @property array $data
 * @property string $path
 *
 */
class ProgressBar
{
	private $key;

	private $data;
	private $path;

	private static $useFile = true;

	public function __construct($key)
	{
		$this->key = $key;
		$this->path = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR;
	}

	public function start($total, $redirect)
	{
		$this->data['running'] = 1;
		$this->data['done'] = 0;
		$this->data['total'] = $total;
		$this->data['redirect'] = $redirect;
		$this->data['error'] = 0;
		$this->data['errorMessage'] = '';
		$this->put();
	}

	public function stop($message)
	{
		$this->data['running'] = 0;
		$this->data['error'] = 1;
		$this->data['errorMessage'] = $message;
		$this->put();
	}

	public function complete()
	{
		$this->data['running'] = 0;
		$this->data['done'] = $this->data['total'];
		$this->data['error'] = 0;
		$this->data['errorMessage'] = '';
		$this->put();
	}

	public function inc($step=1)
	{
		$this->data['done'] += $step;
		$this->put();
	}

	public function put()
	{
		if (self::$useFile)
		{
			file_put_contents($this->path . "{$this->key}", json_encode($this->data));
		}
		else
		{
			$ret = Yii::$app->cache->set($this->key,
			[
				'running' => $this->data['running'],
				'total' => $this->data['total'],
				'done' => $this->data['done'],
				'redirect' => $this->data['redirect'],
				'error' => $this->data['error'],
				'errorMessage' => $this->data['errorMessage']
			],
			1 * 60);
		}
	}

	public static function get($key)
	{
		$path = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR;
		$file = $path . "{$key}";

		if (self::$useFile)
		{
			if (file_exists($file))
			{
				$data = json_decode(file_get_contents($file), true);
				if ($data['running'] == 0)
				{
					unlink($file);
				}
			}
			else
			{
				$data = ['running' => 1, 'total' => 100, 'done' => 0, 'redirect' => '', 'error' => 0, 'errorMessage' => ''];
			}
		}
		else
		{
			$data = Yii::$app->cache->get($key);
			if($data === false)
			{
				$data = ['running' => 1, 'total'=> 100, 'done'=> 0, 'redirect' => '', 'error' => 0, 'errorMessage' => ''];
			}
		}
		return $data;
	}
}