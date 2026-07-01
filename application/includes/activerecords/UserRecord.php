<?php

namespace TomNiemantsverdriet\MannenweekendBingo\ActiveRecords;

use Lumi\Core\Model\ActiveRecord;

/**
 * UserRecord class.
 *
 * ActiveRecord representation of a single participant.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class UserRecord extends ActiveRecord
{
    /**
     * Returns whether this user has notifications enabled
     * @return bool True when a notification endpoint is stored
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function hasNotifications(): bool
    {
        return !empty($this->getNotificationUrl());
    }

    /**
     * Returns the data that is exposed through the public API. The UUID is deliberately
     * omitted as it doubles as a login token.
     * @return array The API data for this user
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getAPIData(): array
    {
        return [
            'id' => $this->getID(),
            'name' => $this->getName(),
            'thumbnail' => reroute(UPLOADS_PATH . '/' . $this->getThumbnail()),
            'notifications_enabled' => $this->hasNotifications(),
        ];
    }
}
