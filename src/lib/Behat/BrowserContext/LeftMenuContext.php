<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;

class LeftMenuContext implements Context
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu */
    private $leftMenu;

    public function __construct(LeftMenu $leftMenu)
    {
        $this->leftMenu = $leftMenu;
    }

    /**
     * @When I click on the left menu bar button :buttonName
     */
    public function iClickLeftMenuBarButton(string $buttonName): void
    {
        $this->leftMenu->clickButton($buttonName);
    }
}
