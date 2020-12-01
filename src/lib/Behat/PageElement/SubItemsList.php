<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Behat\Mink\Session;
use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Context\OldBrowserContext;
use EzSystems\Behat\Browser\Factory\ElementFactory;
use EzSystems\Behat\Browser\Element\Element;
use EzSystems\Behat\Browser\Page\Browser;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\SubitemsGrid;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\TableInterface;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SubitemsGridList;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Tables\SubItemsTable;
use PHPUnit\Framework\Assert;

class SubitemsList extends Component
{
    /** @var Table */
    protected $table;

    protected $isGridViewEnabled;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\SubitemsGrid */
    private $grid;

    public function __construct(Browser $browser, Table $table, SubitemsGrid $grid)
    {
        parent::__construct($browser);
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

        if ($isSortedDescending && $ascending) {
            $this->getHTMLPage()->findAll($this->getLocator('horizontalHeaders'))->getByText($columnName)->click();
        }

        $verificationLocator = $ascending ?
            $this->getLocator('sortingOrderAscending') : $this->getLocator('sortingOrderDescending');

        $this->getHTMLPage()->find($verificationLocator)->assert()->isVisible();
    }

    public function shouldHaveGridViewEnabled(bool $enabled): void
    {
        $this->isGridViewEnabled = $enabled;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('table'))->isVisible());
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

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('table', '.m-sub-items'),
            new VisibleCSSLocator('horizontalHeaders', '.m-sub-items .c-table-view__cell--head'),
            new VisibleCSSLocator('sortingOrderAscending', '.m-sub-items .c-table-view__cell--head.m-sub-items .c-table-view__cell--sorted-asc'),
            new VisibleCSSLocator('sortingOrderDescending', '.m-sub-items .c-table-view__cell--head.m-sub-items .c-table-view__cell--sorted-desc'),
        ];
    }
}
