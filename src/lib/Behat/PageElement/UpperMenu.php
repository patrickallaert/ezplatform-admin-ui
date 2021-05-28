<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Element\Element;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

/** Element that describes upper menu (Content, Admin, Page and theirs children) */
class UpperMenu extends Component
{
    public function goToTab(string $tabName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('menuButton'))->getByText($tabName)->click();
    }

    public function goToDashboard(): void
    {
        $this->getHTMLPage()->find($this->getLocator('dashboardLink'))->click();
    }

    public function goToSubTab(string $tabName): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('submenuButton'))->getByText($tabName)->click();
    }

    public function getNotificationsCount(): int
    {
        return (int) $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('pendingNotificationsCount'))
            ->getAttribute('data-count');
    }

    public function chooseFromUserDropdown(string $option): void
    {
        $this->getHTMLPage()->find($this->getLocator('userSettingsToggle'))->click();
        $this->getHTMLPage()->findAll($this->getLocator('userSettingsItem'))->getByText($option)->click();
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('menuButton'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('menuButton', '.ez-main-nav .nav-link'),
            new VisibleCSSLocator('submenuButton', '.ez-main-sub-nav .nav-link'),
            new VisibleCSSLocator('dashboardLink', '.navbar-brand'),
            new VisibleCSSLocator('pendingNotificationsCount', '.ez-user-menu__name-wrapper .n-pending-notifications'),
            new VisibleCSSLocator('userSettingsToggle', '.ez-user-menu__name-wrapper'),
            new VisibleCSSLocator('userSettingsItem', '.ez-user-menu__item'),
        ];
    }
}
