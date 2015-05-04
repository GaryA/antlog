<?php

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);
$I->see('AntLog 3');
$I->seeLink('About');
$I->click('About');
$I->see('About', 'h1');
