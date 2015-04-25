<?php
namespace app\rbac;

use yii\rbac\Rule;
use app\models\Team;

/**
 * Checks if userID matches the user of the team passed in via params
 */
class OwnTeamRule extends Rule
{
	public $name = 'isOwnTeam';
	
	/**
	 * @param string|integer $user the user ID
	 * @param Item $item the role or permission that this rule is associated with
	 * @param array $params parameters passedto ManagerInterface::checkAccess()
	 * @return boolean a value indicating whether the rule permits the role or permission it is assocated with
	 */
	public function execute($user, $item, $params)
	{
		if (isset($params['teamId']))
		{
			$team = Team::findOne(['userId' => $user]);
			if ($params['teamId'] == $team->id)
			{
				return true;
			}
		}
		return false;
	}
}
