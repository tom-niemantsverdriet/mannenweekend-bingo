<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Sense\Launcher\SenseAppController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\Square;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\SquareCompleted;

/**
 * SenseSquareCompletedController class.
 *
 * Sense admin controller that lets the user manage completed squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SenseSquareCompletedController extends SenseAppController
{
    /**
     * Shows an overview of all completed squares
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => SquareCompleted::getModel(),
            'template' => 'table',
            'columns' => ['completed_at', 'square', 'offender', 'posted_by'],
            'source' => SquareCompleted::findAll()->sort(['completed_at' => -1]),
            'title' => 'Voltooide vakjes',
            'delete-message' => 'Weet je zeker dat je deze registratie wilt verwijderen?',
        ]);


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to add a new completion
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $this->configureForm();
        $this->setTitle('Vakje afvinken');


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to edit an existing completion
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('arguments.0');

        $id;

        $this->configureForm();
        $this->setTitle('Registratie bewerken');


        return $this->respondWithTemplate();
    }

    /**
     * Configures the shared form template for a completion
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function configureForm(): void
    {
        $this->setProperties([
            'model' => SquareCompleted::getModel(),
            'template' => 'form',
            'title' => false,
            'return-url' => '/sense-square-completed',
            'columns' => [
                'is-card animate-in' => [
                    'Vakje' => Square::findAll()->sort(['position' => 1]),
                    'Overtreder' => ['offender' => User::findAll()->sort(['name' => 1])],
                    'Geregistreerd door' => ['posted_by' => User::findAll()->sort(['name' => 1])],
                    'Wanneer' => 'completed_at',
                    'Reden' => 'reason',
                ],
            ],
        ]);
    }

    /**
     * Configures the delete template for a completion
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => SquareCompleted::getModel(),
            'template' => 'delete',
            'type-name' => 'Registratie',
        ]);


        return $this->respondWithTemplate();
    }
}
