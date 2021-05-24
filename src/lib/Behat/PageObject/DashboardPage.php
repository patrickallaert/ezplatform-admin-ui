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

class DashboardPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    protected $table;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, Table $table)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->table = $table->withParentLocator($this->getLocator('table'));
    }

    public function switchTab(string $tableName, string $tabName)
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('tableTitle'))->getByText($tableName)
            ->findAll($this->getLocator('tableTab'))->getByText($tabName)
            ->click();
    }

    public function isListEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    public function editDraft(string $contentDraftName)
    {
        $this->table->getTableRow(['Name' => $contentDraftName])->edit();
    }

    public function isDraftOnList(string $draftName): bool
    {
        return $this->table->hasElement(['Name' => $draftName]);
    }

    protected function getRoute(): string
    {
        return 'dashboard';
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('pageTitle'))->assert()->textEquals('My dashboard');
        $this->getHTMLPage()->find($this->getLocator('table'))->assert()->isVisible();
    }

    public function getName(): string
    {
        return 'Dashboard';
    }

    protected function specifyLocators(): array
    {
        return [
                new VisibleCSSLocator('tableSelector', '.ez-card'),
                new VisibleCSSLocator('tableTitle', '.ez-card__title'),
                new VisibleCSSLocator('tableTab', '.ez-tabs .nav-item'),
                new VisibleCSSLocator('pageTitle', '.ez-header h1'),
                new VisibleCSSLocator('table', '#ez-tab-list-content-dashboard-my .tab-pane.active'),
        ];
    }
}
