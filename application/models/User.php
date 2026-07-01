<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Models;

use Lumi\Core\Model;
use TomNiemantsverdriet\MannenweekendBingo\ActiveRecords\UserRecord;

/**
 * User class.
 *
 * Model that represents a participant of the weekend that can complete or report squares.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class User extends Model
{
    /**
     * Configures the user schema
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function onStart(): void
    {
        $this->setColumns([
            'user_id' => ['is-auto-id' => true],
            'name' => ['limit' => 100, 'required' => true],
            'uuid' => ['limit' => 64],
            'thumbnail' => ['limit' => 255],
            'notification_url' => 'text',
        ]);

        $this->setActiveRecordClass(UserRecord::class);
    }

    /**
     * Inserts a new user. Generates a UUID when none is provided.
     * @param array $data The data to insert
     * @return string|int|bool The identifier of the new user
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function insert($data)
    {
        if (empty($data['uuid'])) {
            $data['uuid'] = $this->generateUuid();
        }

        return parent::insert($data);
    }

    /**
     * Generates a random version 4 UUID
     * @return string The generated UUID
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    private function generateUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
