<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Models;

use Lumi\Core\Model;
use TomNiemantsverdriet\MannenweekendBingo\ActiveRecords\SquareRecord;

/**
 * Square class.
 *
 * Model that represents a single square (objective) on the bingo card.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class Square extends Model
{
    /**
     * Configures the square schema
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function onStart(): void
    {
        $this->setColumns([
            'square_id' => ['is-auto-id' => true],
            'objective' => ['limit' => 255, 'required' => true],
            'position' => ['data-type' => 'int', 'default' => 0],
        ]);

        $this->setActiveRecordClass(SquareRecord::class);
    }
}
