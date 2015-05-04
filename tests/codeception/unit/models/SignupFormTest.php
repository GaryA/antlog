<?php

namespace tests\codeception\unit\models;

use tests\codeception\unit\DbTestCase;
use tests\codeception\fixtures\UserFixture;
use Codeception\Specify;
use app\models\SignupForm;

class SignupFormTest extends DbTestCase
{

    use Specify;

    public function testCorrectSignup()
    {
        $model = new SignupForm([
            'username' => 'test_user2',
            'email' => 'user2@test.com',
            'password' => 'password',
			'team_name' => 'Team Test 2'
        ]);

        $user = $model->signup();

        $this->assertInstanceOf('app\models\User', $user, 'user should be valid');

        expect('username should be correct', $user->username)->equals('test_user2');
        expect('email should be correct', $user->email)->equals('user2@test.com');
        expect('password should be correct', $user->validatePassword('password'))->true();
    }

    public function testNotCorrectSignup()
    {
        $model = new SignupForm([
            'username' => 'troy.becker',
            'email' => 'nicolas.dianna@hotmail.com',
            'password' => 'some_password',
        ]);

        expect('username and email are in use, user should not be created', $model->signup())->null();
    }

    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                'dataFile' => '@tests/codeception/unit/fixtures/data/models/user.php',
            ],
        ];
    }

}
