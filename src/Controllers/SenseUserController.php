<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): void
    {
        $this->setProperties([
            'model' => User::getModel(),
            'template' => 'table',
            'columns' => ['name', 'uuid'],
            'source' => User::findAll()->sort(['name' => 1]),
            'title' => 'Deelnemers',
            'delete-message' => 'Weet je zeker dat je deze deelnemer wilt verwijderen?',
        ]);
    }

    /**
     * Shows the form to add a new participant
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(): void
    {
        $this->configureForm();
        $this->setTitle('Deelnemer toevoegen');
    }

    /**
     * Shows the form to edit an existing participant
     * @param int $id The identifier of the participant
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit($id): void
    {
        $id;

        $this->configureForm();
        $this->setTitle('Deelnemer bewerken');
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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(): void
    {
        $this->setProperties([
            'model' => User::getModel(),
            'template' => 'delete',
            'type-name' => 'Deelnemer',
        ]);
    }
}
