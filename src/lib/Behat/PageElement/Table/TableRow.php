<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Table;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\LocatorCollection;
use EzSystems\Behat\Browser\Locator\LocatorInterface;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class TableRow extends Component
{
    /** @var \EzSystems\Behat\Browser\Element\ElementInterface */
    private $element;

    /** @var \EzSystems\Behat\Browser\Locator\LocatorCollection */
    private $locatorCollection;

    public function __construct(Session $session, MinkParameters $minkParameters, ElementInterface $element, LocatorCollection $locatorCollection)
    {
        parent::__construct($session, $minkParameters);
        $this->element = $element;
        $this->locatorCollection = $locatorCollection;
    }

    public function goToItem(): void
    {
        $this->element->find($this->getLocator('link'))->click();
    }

    public function select(): void
    {
        $this->element->find($this->getLocator('checkbox'))->click();
    }

    public function edit(): void
    {
        $this->element->find($this->getLocator('edit'))->click();
    }

    public function assign(): void
    {
        $this->element->find($this->getLocator('assign'))->click();
    }

    public function getCellValue(string $headerName): string
    {
        return $this->element->find($this->locatorCollection->get($headerName))->getText();
    }

    public function verifyIsLoaded(): void
    {
    }

    public function click(LocatorInterface $locator)
    {
        return $this->element->find($locator)->click();
    }

    public function canBeSelected(): bool
    {
        return $this->element->find($this->getLocator('checkbox'))->getAttribute('disabled') !== 'disabled';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('link', 'a'),
            new VisibleCSSLocator('checkbox', 'input[type=checkbox]'),
            new VisibleCSSLocator('assign', '[data-original-title="Assign content"],[data-original-title="Assign to Users/Groups"]'),
            new VisibleCSSLocator('edit', '.ez-icon-edit,[data-original-title="Edit"]'),
        ];
    }
}
