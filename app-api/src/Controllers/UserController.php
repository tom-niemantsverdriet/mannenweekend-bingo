<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

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
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $result = [];

        foreach (User::findAll()->sort(['name' => 1]) as $user) {
            $result[] = $user->getAPIData();
        }

        return $this->respondWithData( $result);
    }

    /**
     * Returns the intentionally public routes for this ActionController.
     * @return array The public routes
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function getRoutes(): array
    {
        return [
            ControllerRoute::get('/user/index', 'index'),
        ];
    }
}
