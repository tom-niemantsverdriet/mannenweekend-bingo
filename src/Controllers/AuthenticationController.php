<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

use Lumi\Core\ActionController;
use Lumi\SessionManager\Session;
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
class AuthenticationController extends ActionController
{
    /**
     * Authenticates the participant with the given UUID by storing their identifier in the session.
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $uuid = $request->getAttribute('arguments.0');

        $user = User::findSingleByFilters(['uuid' => $uuid]);

        if ($user !== null) {
            $_SESSION['user_id'] = $user->getID();
        }

        $session = Session::getInstance();
        $session->upgradeToPersistent();

        return $this->respondWithRedirect(reroute('/'));
    }

    /**
     * Returns the intentionally public routes for this ActionController.
     * @return array The public routes
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function getRoutes(): array
    {
        return [
            ControllerRoute::get('/authentication/login/{uuid}', 'login'),
        ];
    }
}
