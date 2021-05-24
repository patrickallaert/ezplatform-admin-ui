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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class TrashPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    public $dialog;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu */
    private $rightMenu;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    public function __construct(
        Session $session, MinkParameters $minkParameters, Router $router,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        Dialog $dialog,
        RightMenu $rightMenu,
        Table $table)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->dialog = $dialog;
        $this->rightMenu = $rightMenu;
        $this->table = $table;
    }

    public function hasElement(string $itemType, string $itemName): bool
    {
        return $this->table->hasElement(['Name' => $itemName, 'Content type' => $itemType]);
    }

    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    public function restoreSelectedNewLocation(string $pathToContent)
    {
        $this->getHTMLPage()->find($this->getLocator('restoreUnderNewLocationButton'))->click();
        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($pathToContent);
        $this->universalDiscoveryWidget->confirm();
    }

    public function emptyTrash()
    {
        $this->rightMenu->clickButton('Empty Trash');
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function deleteSelectedItems()
    {
        $this->getHTMLPage()->find($this->getLocator('trashButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function select(array $parameters)
    {
        $this->table->getTableRow($parameters)->select();
    }

    public function restoreSelectedItems()
    {
        $this->getHTMLPage()->find($this->getLocator('restoreButton'))->click();
    }

    protected function getRoute(): string
    {
        return 'trash/list';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Trash',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Trash';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-page-title h1'),
            new VisibleCSSLocator('restoreButton', '#trash_item_restore_restore'),
            new VisibleCSSLocator('trashButton', '#delete-trash-items'),
            new VisibleCSSLocator('restoreUnderNewLocationButton', '#trash_item_restore_location_select_content'),
            ];
    }
}
