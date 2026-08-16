<?php

namespace TomNiemantsverdriet\MannenweekendBingo\Sense;

use Lumi\Sense\Launcher\SenseApp;

/**
 * UserMenuItem class.
 *
 * Registers the participants manager as an app in the Sense launcher.
 *
 * @author Tom Niemantsverdriet <tom@lumitec.nl>
 */
class UserMenuItem extends SenseApp
{
    /**
     * Returns whether the app is accessible for the current user.
     * @return bool True if the app is accessible
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function isAccessible(): bool
    {
        return true;
    }

    /**
     * Returns whether the app appears in the dock by default.
     * @return bool True so the app is pinned by default
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function isDefault(): bool
    {
        return true;
    }

    /**
     * Returns the icon of the app.
     * @return string The icon
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getIcon(): string
    {
        return 'icon-group';
    }

    /**
     * Returns the URL the app opens.
     * @return string The URL
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getUrl(): string
    {
        return 'sense-user';
    }

    /**
     * Returns the title of the app.
     * @return string The title
     * @author Tom Niemantsverdriet <tom@lumitec.nl>
     */
    public function getTitle(): string
    {
        return translate('Deelnemers');
    }
}
