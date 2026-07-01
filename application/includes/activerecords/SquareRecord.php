<?php

namespace TomNiemantsverdriet\MannenweekendBingo\ActiveRecords;

use Lumi\Core\Model\ActiveRecord;

/**
 * SquareRecord class.
 *
 * ActiveRecord representation of a single bingo square.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareRecord extends ActiveRecord
{
    /**
     * Returns the data that is exposed through the API
     * @return array The API data for this square
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getAPIData(): array
    {
        return [
            'id' => $this->getID(),
            'objective' => $this->getObjective(),
            'position' => (int) $this->getPosition(),
        ];
    }
}
