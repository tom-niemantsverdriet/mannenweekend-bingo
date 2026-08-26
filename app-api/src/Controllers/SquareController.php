<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

use TomNiemantsverdriet\MannenweekendBingo\AppAPI\APIController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\Square;

/**
 * SquareController class.
 *
 * API controller that returns the bingo squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareController extends APIController
{
    /**
     * Returns an overview of all squares
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $result = [];

        foreach (Square::findAll()->sort(['position' => 1]) as $square) {
            $result[] = $square->getAPIData();
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
            ControllerRoute::get('/square', 'index'),
        ];
    }
}
