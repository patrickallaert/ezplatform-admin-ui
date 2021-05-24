<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\TableNavigationTab;
use PHPUnit\Framework\Assert;

class SystemInfoPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\TableNavigationTab */
    protected $tableNavigationTab;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\TableInterface */
    private $table;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, TableNavigationTab $tableNavigationTab, Table $table)
    {
        parent::__construct($session, $minkParameters, $router);

        $this->tableNavigationTab = $tableNavigationTab;
        $this->table = $table
            ->withParentLocator($this->getLocator('packagesTable'))
            ->endConfiguration();
    }

    public function goToTab(string $tabName)
    {
        $this->tableNavigationTab->goToTab($tabName);
    }

    public function verifyCurrentTableHeader(string $header)
    {
        $this->getHTMLPage()->find($this->getLocator('tableTitle'))->assert()->textEquals($header);
    }

    public function verifyPackages(array $packages)
    {
        $actualPackageData = $this->table->getColumnValues(['Name']);
        $names = array_column($actualPackageData, 'Name');

        foreach ($packages as $package) {
            Assert::assertContains($package, $names);
        }
    }

    public function verifyBundles(array $bundleNames)
    {
        $this->verifyPackages($bundleNames);
    }

    protected function getRoute(): string
    {
        return '/systeminfo';
    }

    public function verifyIsLoaded(): void
    {
        $this->tableNavigationTab->verifyIsLoaded();
        $this->verifyCurrentTableHeader('Product');
    }

    public function getName(): string
    {
        return 'System Information';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('tableTitle', '.tab-pane.active .ez-fieldgroup__name'),
            new VisibleCSSLocator('packagesTable', '.tab-pane.active .ez-fieldgroup:nth-of-type(2)'),
        ];
    }
}
