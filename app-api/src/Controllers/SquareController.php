<?php

namespace TomNiemantsverdriet\MannenweekendBingo\AppAPI\Controllers;

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
     * @return array The list of squares
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function index(): array
    {
        $result = [];

        foreach (Square::findAll()->sort(['position' => 1]) as $square) {
            $result[] = $square->getAPIData();
        }

        return $result;
    }
}
