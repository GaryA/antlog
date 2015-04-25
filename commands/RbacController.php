<?php
namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

		// add roles for teams and admin
		$rule = new \app\rbac\UserGroupRule;
		$auth->add($rule);

		$team = $auth->createRole('team');
		$team->ruleName = $rule->name;
		$auth->add($team);

		$admin = $auth->createRole('admin');
		$admin->ruleName = $rule->name;
		$auth->add($admin);
		$auth->addChild($admin, $team);

		$rule = new \app\rbac\OwnTeamRule;
		$auth->add($rule);

		// anyone can create a team, so no rule for that

		// add "updateOwnRobot" permission
		$updateOwnRobot = $auth->createPermission('updateOwnRobot');
		$updateOwnRobot->description = 'Update own robot';
		$updateOwnRobot->ruleName = $rule->name;
		$auth->add($updateOwnRobot);
		
        // add "createRobot" permission
        $createRobot = $auth->createPermission('createRobot');
        $createRobot->description = 'Create a robot';
        $auth->add($createRobot);

        // add "createEvent" permission
        $createEvent = $auth->createPermission('createEvent');
        $createEvent->description = 'Create an event';
        $auth->add($createEvent);

        // add "drawEvent" permission
        $drawEvent = $auth->createPermission('drawEvent');
        $drawEvent->description = 'Do the draw for an event';
        $auth->add($drawEvent);

        // add "runEvent" permission
        $runEvent = $auth->createPermission('runEvent');
        $runEvent->description = 'Run an event';
        $auth->add($runEvent);

		// add "addResult" permission
		$addResult = $auth->createPermission('addResult');
		$addResult->description = 'Add a fight result';
		$auth->add($addResult);

		// add "addEntrant" permission
		$addEntrant = $auth->createPermission('addEntrant');
		$addEntrant->description = 'Add an entrant';
		$auth->add($addEntrant);

        // add permissions to "team" role
        $auth->addChild($team, $createRobot);
		$auth->addChild($team, $updateOwnRobot);

        // add permissions to "admin" role
        $auth->addChild($admin, $createEvent);
        $auth->addChild($admin, $drawEvent);
        $auth->addChild($admin, $runEvent);
        $auth->addChild($admin, $addResult);
        $auth->addChild($admin, $addEntrant);

    }
}