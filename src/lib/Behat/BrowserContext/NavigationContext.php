<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use EzSystems\Behat\Browser\Page\PageRegistry;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentViewPage;

class NavigationContext implements Context
{
    /** @var \EzSystems\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    /** @var \EzSystems\Behat\Browser\Page\PageRegistry[] */
    private $pageRegistry;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu */
    private $upperMenu;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Breadcrumb */
    private $breadcrumb;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentViewPage */
    private $contentViewPage;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    public function __construct(
        ArgumentParser $argumentParser,
        UpperMenu $upperMenu,
        Breadcrumb $breadcrumb,
        ContentViewPage $contentViewPage,
        PageRegistry $pageRegistry,
        ContentUpdateItemPage $contentUpdateItemPage
    ) {
        $this->argumentParser = $argumentParser;
        $this->pageRegistry = $pageRegistry;
        $this->upperMenu = $upperMenu;
        $this->breadcrumb = $breadcrumb;
        $this->contentViewPage = $contentViewPage;
        $this->contentUpdateItemPage = $contentUpdateItemPage;
    }

    /**
     * @Given I open :pageName page in admin SiteAccess
     * @Given I open the :pageName page in admin SiteAccess
     */
    public function openPage(string $pageName): void
    {
        $page = $this->pageRegistry->get($pageName);
        $page->open('admin');
        $page->verifyIsLoaded();
    }

    /**
     * @Given I try to open :pageName page in admin SiteAccess
     */
    public function tryToOpenPage(string $pageName): void
    {
        $this->pageRegistry->get($pageName)->tryToOpen('admin');
    }

    /**
     * @Then /^I should be on "?([^\"]*)"? page$/
     */
    public function iAmOnPage(string $pageName): void
    {
        $this->pageRegistry->get($pageName)->verifyIsLoaded();
    }

    /**
     * @Then I go to :tab tab
     * @Then I go to :subTab in :tab tab
     */
    public function iGoToTab(string $tabName, string $subTab = null): void
    {
        $this->upperMenu->goToTab($tabName);

        if ($subTab !== null) {
            $this->upperMenu->goToSubTab($subTab);
        }
    }

    /**
     * @When I click on :element on breadcrumb
     */
    public function iClickOnBreadcrumbLink(string $element): void
    {
        $this->breadcrumb->verifyIsLoaded();
        $this->breadcrumb->clickBreadcrumbItem($element);
    }

    /**
     * @Given I navigate to content :contentName of type :contentType in :path
     */
    public function iNavigateToContent(string $contentName, string $contentType, string $path = null)
    {
        $expectedContentPath = sprintf('%s/%s', $path, $contentName);
        $pathParts = explode('/', $expectedContentPath);
        if ($pathParts[0] === 'root') {
            $menuTab = 'Content structure';
            $startingLocation = '/';
        } else {
            $menuTab = $pathParts[0];
            $startingLocation = $pathParts[0];
        }
        $expectedContentPath = $this->argumentParser->replaceRootKeyword($expectedContentPath);
        $this->contentViewPage->setExpectedLocationPath($startingLocation);
        $this->contentViewPage->navigateToPath($expectedContentPath, $menuTab);
    }

    /**
     * @Given I go to user notifications
     */
    public function iGoToUserNotifications()
    {
        $this->upperMenu->chooseFromUserDropdown('View Notifications');
    }

    /**
     * @Given I'm on Content view Page for :path
     * @Given there exists Content view Page for :path
     */
    public function iMOnContentViewPageFor(string $path)
    {
        $path = $this->argumentParser->parseUrl($path);
        $this->contentViewPage->setExpectedLocationPath($path);
        $this->contentViewPage->open('admin');
        $this->contentViewPage->verifyIsLoaded();
    }

    /**
     * @Given I should be on Content view Page for :path
     */
    public function iShouldBeOnContentViewPage(string $path)
    {
        $path = $this->argumentParser->parseUrl($path);
        $this->contentViewPage->setExpectedLocationPath($path);
        $this->contentViewPage->verifyIsLoaded();
    }

    /**
     * @Given I should be on Content update page for :contentItemName
     */
    public function iShouldBeOnContentUpdatePageForItem(string $contentItemName)
    {
        $this->contentUpdateItemPage->setExpectedPageTitle($contentItemName);
        $this->contentUpdateItemPage->verifyIsLoaded();
    }
}
