<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Sense\Launcher\SenseAppController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\Square;

/**
 * SenseSquareController class.
 *
 * Sense admin controller that lets the user manage the bingo squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SenseSquareController extends SenseAppController
{
    /**
     * Shows an overview of all squares
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => Square::getModel(),
            'template' => 'table',
            'columns' => ['objective'],
            'sortable' => true,
            'title' => 'Vakjes',
            'delete-message' => 'Weet je zeker dat je dit vakje wilt verwijderen?',
        ]);


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to add a new square
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $this->configureForm();
        $this->setTitle('Vakje toevoegen');


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to edit an existing square
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('arguments.0');

        $id;

        $this->configureForm();
        $this->setTitle('Vakje bewerken');


        return $this->respondWithTemplate();
    }

    /**
     * Configures the shared form template for a square
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function configureForm(): void
    {
        $this->setProperties([
            'model' => Square::getModel(),
            'template' => 'form',
            'title' => false,
            'return-url' => '/sense-square',
            'columns' => [
                'is-card animate-in' => [
                    'Opdracht' => 'objective',
                    'Positie' => 'position',
                ],
            ],
        ]);
    }

    /**
     * Configures the delete template for a square
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => Square::getModel(),
            'template' => 'delete',
            'type-name' => 'Vakje',
        ]);


        return $this->respondWithTemplate();
    }
}
