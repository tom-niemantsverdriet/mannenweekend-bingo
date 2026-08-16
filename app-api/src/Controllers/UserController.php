<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * UserController class.
 *
 * API controller that returns the participants.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class UserController extends APIController
{
    /**
     * Returns an overview of all participants
     * @return array The list of participants
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): array
    {
        $result = [];

        foreach (User::findAll()->sort(['name' => 1]) as $user) {
            $result[] = $user->getAPIData();
        }

        return $result;
    }
}
