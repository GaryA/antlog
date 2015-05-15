<?php

namespace tests\codeception\functional;

use tests\codeception\_pages\UserUpdatePage;
use tests\codeception\_pages\LoginPage;
use app\models\User;

class UpdateUserCest
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

	/**
	 * @before login
	 * @param \codeception_frontend\FunctionalTester $I
	 * @param \Codeception\Scenario $scenario
	 */
	public function testUserUpdate($I, $scenario)
	{
		$I->wantTo('ensure that user/team update works');

		$I->click('test_user');
		$I->see('Home', 'li');
		$I->see('Teams', 'li');
		$I->see('Team Test', 'li');
		$I->click('Update');
		$I->see('Update Team:', 'h1');

		$I->see('Home', 'li');
		$I->see('Teams', 'li');
		$I->see('Team Test', 'li');
		$I->see('Update', 'li');

		$I->amGoingTo('submit update form with correct data');
		$I->fillField('UpdateForm[username]', 'test_user2');
		$I->fillField('UpdateForm[email]', 'user2@test.com');
		$I->fillField('UpdateForm[team_name]', 'Team Test 2');
		$I->click('Update');

		$I->expectTo('see that user/team is updated');
		$I->seeRecord('app\models\User', [
			'username' => 'test_user2',
			'email' => 'user2@test.com',
			'team_name' => 'Team Test 2',
		]);

		$I->expectTo('see that user/team is updated');
		$I->see('Team Test 2', 'h1');
		$I->see('Home', 'li');
		$I->see('Teams', 'li');
		$I->see('Team Test 2', 'li');
		$I->see('Updated user model');

		// undo the changes!
		$I->click('Update');
		$I->fillField('UpdateForm[username]', 'test_user');
		$I->fillField('UpdateForm[email]', 'user@test.com');
		$I->fillField('UpdateForm[team_name]', 'Team Test');
		$I->click('Update');

	}
}
