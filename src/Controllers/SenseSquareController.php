<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Controllers;

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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): void
    {
        $this->setProperties([
            'model' => Square::getModel(),
            'template' => 'table',
            'columns' => ['objective'],
            'sortable' => true,
            'title' => 'Vakjes',
            'delete-message' => 'Weet je zeker dat je dit vakje wilt verwijderen?',
        ]);
    }

    /**
     * Shows the form to add a new square
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function add(): void
    {
        $this->configureForm();
        $this->setTitle('Vakje toevoegen');
    }

    /**
     * Shows the form to edit an existing square
     * @param int $id The identifier of the square
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function edit($id): void
    {
        $id;

        $this->configureForm();
        $this->setTitle('Vakje bewerken');
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
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function delete(): void
    {
        $this->setProperties([
            'model' => Square::getModel(),
            'template' => 'delete',
            'type-name' => 'Vakje',
        ]);
    }
}
