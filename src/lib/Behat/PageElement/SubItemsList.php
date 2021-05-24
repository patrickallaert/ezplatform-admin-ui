<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Locator\CSSLocator;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\SubitemsGrid;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\TableInterface;
use PHPUnit\Framework\Assert;

class SubItemsList extends Component
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    protected $table;

    protected $isGridViewEnabled;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\SubitemsGrid */
    private $grid;

    public function __construct(Session $session, MinkParameters $minkParameters, Table $table, SubitemsGrid $grid)
    {
        parent::__construct($session, $minkParameters);
        $this->table = $table->withParentLocator($this->getLocator('table'))->endConfiguration();
        $this->grid = $grid;
    }

    public function sortBy(string $columnName, bool $ascending): void
    {
        if ($this->isGridViewEnabled) {
            return;
        }

        $this->getHTMLPage()->findAll($this->getLocator('horizontalHeaders'))->getByText($columnName)->click();
        $isSortedDescending = $this->getHTMLPage()->findAll($this->getLocator('sortingOrderDescending'))->any();

        if (!$isSortedDescending && !$ascending) {
            $this->getHTMLPage()->findAll($this->getLocator('horizontalHeaders'))->getByText($columnName)->click();
        }

        $verificationLocator = $ascending ?
            $this->getLocator('sortingOrderAscending') : $this->getLocator('sortingOrderDescending');

        $this->getHTMLPage()->setTimeout(5)->find($verificationLocator);
    }

    public function shouldHaveGridViewEnabled(bool $enabled): void
    {
        $this->isGridViewEnabled = $enabled;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('table'))->isVisible());
        $this->getHTMLPage()->setTimeout(5)->waitUntil(function() {
            return $this->getHTMLPage()->findAll($this->getLocator('spinner'))->any() === false;
        });
    }

    public function clickListElement(string $contentName, string $contentType)
    {
        $this->getTable()->getTableRow(['Name' => $contentName, 'Content Type' => $contentType])->goToItem();
    }

    public function isElementInTable(array $elementData): bool
    {
        return $this->getTable()->hasElement($elementData);
    }

    protected function getTable(): TableInterface
    {
        return $this->isGridViewEnabled ? $this->grid : $this->table;
    }

    public function goTo(string $itemName): void
    {
        $this->getTable()->getTableRow(['Name' => $itemName])->goToItem();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('table', '.m-sub-items'),
            new VisibleCSSLocator('horizontalHeaders', '.m-sub-items .c-table-view__cell--head'),
            new VisibleCSSLocator('spinner', '.m-sub-items__spinner-wrapper'),
            new CSSLocator('sortingOrderAscending', '.m-sub-items .c-table-view__cell--head.c-table-view__cell--sorted-asc'),
            new CSSLocator('sortingOrderDescending', '.m-sub-items .c-table-view__cell--head.c-table-view__cell--sorted-desc'),
        ];
    }
}
