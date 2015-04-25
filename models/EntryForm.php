<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * EntryForm is the model behind the example form.
 */
class EntryForm extends Model
{
    public $name;
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name and email are required
            [['name', 'email'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }
}
