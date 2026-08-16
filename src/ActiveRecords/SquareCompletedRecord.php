<?php

namespace TomNiemantsverdriet\MannenweekendBingo\ActiveRecords;

use Lumi\Core\Model\ActiveRecord;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\Square;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * SquareCompletedRecord class.
 *
 * ActiveRecord representation of a completed square.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SquareCompletedRecord extends ActiveRecord
{
    /**
     * Returns the square that was completed
     * @return SquareRecord|null The square record or null when it no longer exists
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getSquareRecord(): ?SquareRecord
    {
        return Square::find($this->getSquare());
    }

    /**
     * Returns the user that completed the square
     * @return UserRecord|null The offender or null when the user no longer exists
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getOffenderRecord(): ?UserRecord
    {
        return User::find($this->getOffender());
    }

    /**
     * Returns the user that posted the completion
     * @return UserRecord|null The poster or null when the user no longer exists
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getPostedByRecord(): ?UserRecord
    {
        return User::find($this->getPostedBy());
    }

    /**
     * Returns the data that is exposed through the API
     * @param int $commentCount The number of comments on this completion
     * @return array The API data for this completion
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getAPIData(int $commentCount = 0): array
    {
        // Load referenced records for display fields

        $square = $this->getSquareRecord();
        $offender = $this->getOffenderRecord();
        $postedBy = $this->getPostedByRecord();

        // Return the public completion payload

        return [
            'id' => $this->getID(),
            'completed_at' => $this->getCompletedAt(),
            'square_id' => $this->getSquare(),
            'square' => $square?->getObjective(),
            'offender_id' => $this->getOffender(),
            'offender' => $offender?->getName(),
            'offender_thumbnail' => reroute(UPLOADS_PATH . '/' . $postedBy?->getThumbnail()),
            'posted_by_id' => $this->getPostedBy(),
            'posted_by' => $postedBy?->getName(),
            'reason' => $this->getReason(),
            'comment_count' => $commentCount,
        ];
    }
}
