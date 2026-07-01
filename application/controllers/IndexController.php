<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Lumi\Core\Controller;

/**
 *
 * IndexController class.
 *
 * Main controller that serves the Vue bingo application.
 *
 * @author Tom Niemantsverdriet <tom@flowtogether.nl>
 */
class IndexController extends Controller
{
    /**
     * Serves the Vue application shell
     * @return void The returned value
     * @author Tom Niemantsverdriet <tom@flowtogether.nl>
     */
    public function index(): void
    {
        $this->setMetaTag('viewport', 'width=device-width, initial-scale=1, viewport-fit=cover');

        $this->addScript('build', 'app.build');
        $this->addCSP('script-src', "'unsafe-eval'");
        $this->setTitleAppendix('');

        $metaDescription = 'Hallo ik ben Sfen en welkom bij mijn mannenweekend bingo. Doe mee en win € 1000,- kusjes van Sfen';

        $this->setTitle("Sfen's mannenweekend bingo");
        $this->setMetaTag('description', $metaDescription);
        $this->setMetaTag('og:description', $metaDescription);
        $this->setMetaTag('og:image', reroute(BASE_PATH . '/application/assets/images/thumbnail.jpeg'));
    }
}
