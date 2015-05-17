<?php

namespace tests\codeception\functional;

use tests\codeception\_pages\RobotCreatePage;
use tests\codeception\_pages\LoginPage;
use app\models\Robot;

class RobotCest
{

    /**
     * This method is called before each cest class test method
     * @param \Codeception\Event\TestEvent $event
     */
    public function _before($event)
    {
    }

    /**
     * This method is called after each cest class test method, even if test failed.
     * @param \Codeception\Event\TestEvent $event
     */
    public function _after($event)
    {
        Robot::deleteAll([
            'name' => 'Test Robot',
        ]);
    }

    /**
     * This method is called when test fails.
     * @param \Codeception\Event\FailEvent $event
     */
    public function _fail($event)
    {

    }

	protected function login($I)
	{
		$loginPage = LoginPage::openBy($I);
		$loginPage->login('test_user', 'password');
	}

	protected function logout($I)
	{
		$I->click('Logout (test_user)');
	}

    /**
     * @before login
     * @after logout
     * @param \codeception_frontend\FunctionalTester $I
     * @param \Codeception\Scenario $scenario
     */
    public function testRobotCreate($I, $scenario)
    {
        $I->wantTo('ensure that robot creation works');

        $robotCreatePage = RobotCreatePage::openBy($I);
        $I->see('Create Robot', 'h1');

        $I->amGoingTo('submit new robot form with no data');

        $robotCreatePage->submit([
			'name' => '',
			'typeId' => 0,
			'classId' => 1,
			'active' => 0,
		]);

        $I->expectTo('see validation errors');
        $I->see('Robot Name cannot be blank.', '.help-block');

        $I->amGoingTo('submit new robot form with correct data');
        $robotCreatePage->submit([
            'name' => 'Test Robot 2',
            'typeId' => 1,
			'classId' => 1,
            'active' => 1,
        ]);

        $I->expectTo('see that robot is created');
        $I->seeRecord('app\models\Robot', [
            'name' => 'Test Robot 2',
			'classId' => 1,
            'typeId' => 1,
			'active' => 1,
        ]);
        $I->see('Test Robot 2');
        $I->see('Nanoweight');
        $I->see('Walker');
        $I->see('Yes');

    }

    /**
     * @before login
     * @after logout
     * @param \codeception_frontend\FunctionalTester $I
     * @param \Codeception\Scenario $scenario
     */
    public function testRobotUpdate($I, $scenario)
    {
    	$I->amGoingTo('update existing robot');
    	$I->haveRecord('app\models\Robot', [
    		'id' => 1,
    		'name' => 'Test Robot',
    		'teamId' => 32,
    		'classId' => 1,
    		'typeId' => 1,
    		'active' => 1,
    	]);
    	$I->amOnPage(['robot/view', 'id' => 1]);
    	$I->click('Update');
    	$I->see('Update Robot: Test Robot', 'h1');

    	$I->amGoingTo('change robot name');
    	$I->fillField('Robot[name]', 'New Test Robot');
    	$I->selectOption(['name' => "Robot[classId]"], 2);
    	$I->selectOption(['name' => "Robot[typeId]"], 2);
    	$I->selectOption(['name' => "Robot[active]"], 0);
    	$I->click('Update');

    	$I->expectTo('see that robot name is changed');
    	$I->seeRecord('app\models\Robot', [
    		'name' => 'New Test Robot',
    		'classId' => 2,
    		'typeId' => 2,
    		'active' => 0,
    	]);
    	$I->see('New Test Robot');
    	$I->see('Fleaweight');
    	$I->see('Cluster');
    	$I->see('No');
    }
}
