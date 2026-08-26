<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Core\Routing\Route as ControllerRoute;

use Lumi\Core\ActionController;

/**
 *
 * IndexController class.
 *
 * Main controller that serves the Vue bingo application.
 *
 * @author Tom Niemantsverdriet <tom@flowtogether.nl>
 */
class IndexController extends ActionController
{
    /**
     * Serves the Vue application shell
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->setMetaTag('viewport', 'width=device-width, initial-scale=1, viewport-fit=cover');

        $this->addScript('build', 'app.build');
        $this->addCSP('script-src', "'unsafe-eval'");
        $this->setTitleAppendix('');

        $metaDescription = 'Hallo ik ben Sfen en welkom bij mijn mannenweekend bingo. Doe mee en win € 1000,- kusjes van Sfen';

        $this->setTitle("Sfen's mannenweekend bingo");
        $this->setMetaTag('description', $metaDescription);
        $this->setMetaTag('og:description', $metaDescription);
        $this->setMetaTag('og:image', reroute(BASE_PATH . '/src/Assets/images/thumbnail.jpeg'));


        return $this->respondWithTemplate();
    }

    /**
     * Returns the intentionally public routes for this ActionController.
     * @return array The public routes
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function getRoutes(): array
    {
        return [
            ControllerRoute::get('/', 'index'),
        ];
    }
}
