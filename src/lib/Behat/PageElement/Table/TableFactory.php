<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Table;

use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\LocatorCollection;
use Behat\Mink\Session;

class TableFactory
{
    /** @var Session */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function createRow(ElementInterface $element, LocatorCollection $locatorCollection): TableRow
    {
        return new TableRow(
            $this->session,
            $element,
            $locatorCollection
        );
    }
}
