<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): void
    {
        $this->setProperties([
            'model' => SquareCompleted::getModel(),
            'template' => 'table',
            'columns' => ['completed_at', 'square', 'offender', 'posted_by'],
            'source' => SquareCompleted::findAll()->sort(['completed_at' => -1]),
            'title' => 'Voltooide vakjes',
            'delete-message' => 'Weet je zeker dat je deze registratie wilt verwijderen?',
        ]);
    }

    /**
     * Shows the form to add a new completion
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(): void
    {
        $this->configureForm();
        $this->setTitle('Vakje afvinken');
    }

    /**
     * Shows the form to edit an existing completion
     * @param int $id The identifier of the completion
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit($id): void
    {
        $id;

        $this->configureForm();
        $this->setTitle('Registratie bewerken');
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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(): void
    {
        $this->setProperties([
            'model' => SquareCompleted::getModel(),
            'template' => 'delete',
            'type-name' => 'Registratie',
        ]);
    }
}
