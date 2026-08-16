<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Models;

use Lumi\Core\Model;
use TomNiemantsverdriet\MannenweekendBingo\ActiveRecords\SquareCompletedRecord;

/**
 * SquareCompleted class.
 *
 * Model that records that a square was completed by an offender, as posted by another user.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareCompleted extends Model
{
    /**
     * Configures the square-completed schema
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function onStart(): void
    {
        $this->setColumns([
            'completed_id' => ['is-auto-id' => true],
            'completed_at' => ['data-type' => 'datetime', 'required' => true],
            'square' => ['referenced-table' => 'square', 'required' => true],
            'offender' => ['referenced-table' => 'user', 'required' => true],
            'posted_by' => ['referenced-table' => 'user', 'required' => true],
            'reason' => 'text',
        ]);

        $this->setActiveRecordClass(SquareCompletedRecord::class);
    }

    /**
     * Inserts a new completion. Defaults the completion timestamp to now when none is given.
     * @param array $data The data to insert
     * @return string|int|bool The identifier of the new completion
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function insert($data)
    {
        if (empty($data['completed_at'])) {
            $data['completed_at'] = date('Y-m-d H:i:s');
        }

        return parent::insert($data);
    }
}
