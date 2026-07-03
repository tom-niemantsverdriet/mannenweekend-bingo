<?php

namespace TomNiemantsverdriet\MannenweekendBingo\ActiveRecords;

use Lumi\Core\Model\ActiveRecord;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * CommentRecord class.
 *
 * ActiveRecord representation of a comment on a completed square.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class CommentRecord extends ActiveRecord
{
    /**
     * Returns the user that posted the comment
     * @return UserRecord|null The user record or null when it no longer exists
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getUserRecord(): ?UserRecord
    {
        return User::find($this->getUser());
    }

    /**
     * Returns the data that is exposed through the API
     * @return array The API data for this comment
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getAPIData(): array
    {
        $user = $this->getUserRecord();

        return [
            'id' => $this->getID(),
            'square_completed_id' => $this->getSquareCompleted(),
            'user_id' => $this->getUser(),
            'user' => $user?->getName(),
            'user_thumbnail' => reroute(UPLOADS_PATH . '/' . $user?->getThumbnail()),
            'posted_at' => $this->getPostedAt(),
            'comment' => $this->getComment(),
        ];
    }
}
