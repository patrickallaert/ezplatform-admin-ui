<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use PHPUnit\Framework\Assert;

class SearchPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\TableInterface */
    private $table;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, Table $table)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->table = $table
            ->withParentLocator($this->getLocator('table'))
            ->withEmptyLocator($this->getLocator('emptyTable'))
            ->endConfiguration();
    }

    public function search(string $contentItemName): void
    {
        $this->getHTMLPage()->find($this->getLocator('inputField'))->setValue($contentItemName);
        $this->getHTMLPage()->find($this->getLocator('buttonSearch'))->click();
        $this->verifyIsLoaded();
        $this->getHTMLPage()->find($this->getLocator('table'))->assert()->isVisible();
    }

    public function isElementInResults(array $elementData): bool
    {
        return $this->table->hasElement($elementData);
    }

    protected function getRoute(): string
    {
        return '/search';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Search',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Search';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('inputField', '.ez-search-form #search_query'),
            new VisibleCSSLocator('buttonSearch', '.ez-btn--search'),
            new VisibleCSSLocator('pageTitle', '.ez-page-title .ez-page-title__content-name'),
            new VisibleCSSLocator('table', '.ez-content-container table.table'),
            new VisibleCSSLocator('emptyTable', '.ez-table-header__headline'),
        ];
    }
}
