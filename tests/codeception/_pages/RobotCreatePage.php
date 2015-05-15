<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents contact page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RobotCreatePage extends BasePage
{
    public $route = 'robot/create';

    /**
     * @param array $contactData
     */
    public function submit(array $robotData)
    {
		extract($robotData);
		$this->actor->fillField(['name' => "Robot[name]"], $name);
		$this->actor->selectOption(['name' => "Robot[classId]"], $classId);
		$this->actor->selectOption(['name' => "Robot[typeId]"], $typeId);
		$this->actor->selectOption(['name' => "Robot[active]"], $active);

		$this->actor->click('Create');
    }
}
