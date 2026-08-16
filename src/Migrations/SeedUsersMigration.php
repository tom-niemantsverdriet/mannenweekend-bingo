<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Migrations;

use Lumi\Migrations\Migration;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\User;

/**
 * SeedUsersMigration class.
 *
 * Seeds the participants of the weekend, each with their own generated UUID.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SeedUsersMigration extends Migration
{
    /**
     * @var string[] The names of the participants that should be present
     */
    private const array NAMES = [
        'Sebastiaan',
        'Dennis',
        'Tom',
        'Thom',
        'Ruben',
        'Sfen',
        'Jan-Willem',
        'Jeffrey',
    ];

    /**
     * Inserts every participant that is not present yet, generating a UUID for each.
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function run(): void
    {
        foreach (self::NAMES as $name) {
            if (User::findSingleByFilters(['name' => $name]) !== null) {
                continue;
            }

            User::insert([
                'name' => $name,
                'uuid' => uuid(),
            ]);
        }
    }

    /**
     * Validates that every participant is present after running.
     * @return bool True when all participants exist
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function postvalidate(): bool
    {
        foreach (self::NAMES as $name) {
            if (User::findSingleByFilters(['name' => $name]) === null) {
                return $this->addError("User '$name' was not seeded");
            }
        }

        return true;
    }

    /**
     * Returns the unique name of this migration.
     * @return string The migration name
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getName(): string
    {
        return 'bingo-seed-users';
    }
}
