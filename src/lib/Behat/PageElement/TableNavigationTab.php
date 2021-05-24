<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class TableNavigationTab extends Component
{
    public function getActiveTabName(): string
    {
        return $this->getHTMLPage()->find($this->getLocator('activeNavLink'))->getText();
    }

    public function goToTab(string $tabName): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('navLink'))
            ->filter(function (ElementInterface $element) use ($tabName) {
                return strpos($element->getText(), $tabName) !== false;
            })
            ->first()
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('activeNavLink'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('activeNavLink', '.ez-tabs .active'),
            new VisibleCSSLocator('navLink', '.ez-tabs .nav-link'),
        ];
    }
}