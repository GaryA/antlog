<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
	/**
	 * @var UploadedFile
	 */
	public $uploadFile;
	public $savedFile;

	public function rules()
	{
		return [
				['uploadFile', 'required'],
//				['uploadFile', 'validateFile'],
		];
	}

	public function upload()
	{
		if ($this->validate())
		{
			$this->savedFile = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR  . $this->uploadFile->baseName . '.' . $this->uploadFile->extension;
			$this->uploadFile->saveAs($this->savedFile);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function validateFile($attribute, $params)
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		//$mtype = finfo_file($finfo, Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR  . $this->uploadFile);
		$mtype = finfo_file($finfo, $this->uploadFile);
		finfo_close($finfo);
		if (($mtype == 'text/plain') || ($mtype == 'application/x-sql'))
		{
			if ($this->uploadFile->extension == 'sql')
			{
				return true;
			}
			else
			{
				// wrong extension
				$this->addError($attribute, 'Only SQL files may be uploaded. File extension must be .sql');
			}
		}
		else
		{
			// wrong MIME type
			$this->addError($attribute, 'Only SQL files may be uploaded. MIME type must be application/x-sql or text/plain');
		}
		return false;
	}
}