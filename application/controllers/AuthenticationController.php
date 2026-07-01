<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Lumi\Core\Controller;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * AuthenticationController class.
 *
 * Authenticates a participant based on their personal UUID. A participant opens
 * their personal link (/authentication/authentication/{uuid}) which stores their
 * identifier in the session and forwards them to the bingo card.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class AuthenticationController extends Controller
{
    /**
     * Authenticates the participant with the given UUID by storing their identifier in the session.
     * @param string $uuid The UUID of the participant to authenticate
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function login(string $uuid): void
    {
        $user = User::findSingleByFilters(['uuid' => $uuid]);

        if ($user !== null) {
            $_SESSION['user_id'] = $user->getID();
        }

        redirect('/');
    }
}
