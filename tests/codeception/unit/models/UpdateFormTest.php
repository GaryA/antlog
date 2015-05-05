<?php

namespace tests\codeception\unit\models;

use Yii;
use yii\codeception\TestCase;
use app\models\UpdateForm;
use Codeception\Specify;

class UpdateFormTest extends TestCase
{
    use Specify;

    protected function setUp()
    {
    	$model = new LoginForm([
    		'username' => 'test_user',
    		'password' => 'password',
    	]);

    }
    protected function tearDown()
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testUpdateEmptyForm()
    {
        $model = new UpdateForm([
            'username' => '',
            'email' => '',
        	'team_name' => '',
        ]);

        $this->specify('user should not be able to update, when there is no identity', function () use ($model) {
            expect('model should not update', $model->update())->false();
            expect('error message should be set', $model->errors)->hasKey('username');
        });
    }

    public function testUpdateCorrect()
    {
        $model = new UpdateForm([
            'username' => 'test_user',
            'email' => 'user@test.com',
        	'team_name' => 'Team Test',
        ]);

        $this->specify('user should be able to update with correct credentials', function () use ($model) {
            expect('model should update user', $model->update())->true();
            expect('error message should not be set', $model->errors)->hasntKey('username');
         });
    }

}
