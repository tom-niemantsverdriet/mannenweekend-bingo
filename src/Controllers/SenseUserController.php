<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Lumi\Sense\Launcher\SenseAppController;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * SenseUserController class.
 *
 * Sense admin controller that lets the user manage the participants.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SenseUserController extends SenseAppController
{
    /**
     * Shows an overview of all participants
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => User::getModel(),
            'template' => 'table',
            'columns' => ['name', 'uuid'],
            'source' => User::findAll()->sort(['name' => 1]),
            'title' => 'Deelnemers',
            'delete-message' => 'Weet je zeker dat je deze deelnemer wilt verwijderen?',
        ]);


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to add a new participant
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $this->configureForm();
        $this->setTitle('Deelnemer toevoegen');


        return $this->respondWithTemplate();
    }

    /**
     * Shows the form to edit an existing participant
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('arguments.0');

        $id;

        $this->configureForm();
        $this->setTitle('Deelnemer bewerken');


        return $this->respondWithTemplate();
    }

    /**
     * Configures the shared form template for a participant
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function configureForm(): void
    {
        $this->setProperties([
            'model' => User::getModel(),
            'template' => 'form',
            'title' => false,
            'return-url' => '/sense-user',
            'columns' => [
                'is-card animate-in' => [
                    'Naam' => 'name',
                    'Thumbnail' => 'thumbnail',
                ],
            ],
        ]);
    }

    /**
     * Configures the delete template for a participant
     * @param ServerRequestInterface $request The Controller-local request
     * @return ResponseInterface The Controller response
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $this->setProperties([
            'model' => User::getModel(),
            'template' => 'delete',
            'type-name' => 'Deelnemer',
        ]);


        return $this->respondWithTemplate();
    }
}
