<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Migrations;

use Lumi\Migrations\Migration;
use TomNiemantsverdriet\MannenweekendBingo\Models\Static\Square;

/**
 * SeedSquaresMigration class.
 *
 * Seeds the initial set of bingo squares (objectives) for the weekend.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class SeedSquaresMigration extends Migration
{
    /**
     * @var string[] The objectives that should be present on the bingo card
     */
    private const array OBJECTIVES = [
        'Iemand wordt boos',
        'Iemand schept op over hoeveel seks hij heeft',
        'Iemand zet hardstyle op',
        'Er wordt een foto van Runescape gestuurd',
        'Iemand gaat happen op iemand die hem uitdaagt',
        'Er wordt opgeschept over hoeveel geld iemand heeft',
        'Er wordt gevraagd of iemand de schijven mee heeft',
        'Iemand ruimt niet op',
        'Iemand begint over drugs',
        'Iemand is weer overprikkeld',
        'Iemand maakt een vergelijking met zijn andere vriendengroep',
        'Iemand gelooft iets wat niet waar is',
        'Iemand sloopt iets',
        'Iemand vraagt om een peuk',
        'Iemand begint over een vogelhuisje',
        'Iemand schept over zn kind op',
        'Iemand heeft een sterk verhaal dat niemand gelooft',
        'Iemands kamer meurt naar vieze scheetjes',
        'Iemand vertoont homofobisch gedrag',
        'Je komt niet van iemand af tijdens een gesprek',
        'Er wordt een denigrerende grap over iemands auto gemaakt',
        'Iemand raakt iets kwijt dat bij het huisje hoort',
        'Iemand is het niet eens met de kosten van boodschappen',
        'Iemand begint zelf met de BBQ',
        'Iemand gaat vroeg naar bed',
    ];

    /**
     * Inserts every objective that is not present yet, keeping their order as the position.
     * @return void
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function run(): void
    {
        foreach (self::OBJECTIVES as $position => $objective) {
            if (Square::findSingleByFilters(['objective' => $objective]) !== null) {
                continue;
            }

            Square::insert([
                'objective' => $objective,
                'position' => $position,
            ]);
        }
    }

    /**
     * Validates that every objective is present after running.
     * @return bool True when all objectives exist
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function postvalidate(): bool
    {
        foreach (self::OBJECTIVES as $objective) {
            if (Square::findSingleByFilters(['objective' => $objective]) === null) {
                return $this->addError("Square '$objective' was not seeded");
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
        return 'bingo-seed-squares';
    }
}
