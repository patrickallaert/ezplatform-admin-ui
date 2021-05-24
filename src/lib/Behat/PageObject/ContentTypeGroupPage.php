<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use Behat\Mink\Session;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;

class ContentTypeGroupPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\AdminList */
    protected $adminList;

    /** @var string */
    protected $expectedName;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var mixed */
    private $contentTypeGroupId;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    private $dialog;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, ContentTypeService $contentTypeService, Table $table, Dialog $dialog)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->contentTypeService = $contentTypeService;
        $this->table = $table->withParentLocator($this->getLocator('tableContainer'));
        $this->dialog = $dialog;
    }

    public function hasContentTypes(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('tableItem'))->any();
    }

    public function edit(string $contentTypeName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->edit();
    }

    public function goTo(string $contentTypeName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->goToItem();
    }

    public function createNew(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isContentTypeOnTheList($contentTypeName): bool
    {
        return $this->table->hasElement(['Name' => $contentTypeName]);
    }

    public function delete(string $contentTypeName)
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function hasAssignedContentItems(string $contentTypeGroupName): bool
    {
        return $this->table->getTableRow(['Name' => $contentTypeGroupName])->getCellValue('Number of Content Types') > 0;
    }

    protected function getRoute(): string
    {
        return sprintf('/contenttypegroup/%d', $this->contentTypeGroupId);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals($this->expectedName);
        $this->getHTMLPage()
            ->find($this->getLocator('listHeader'))
            ->assert()->textEquals(sprintf("Content Types in '%s'", $this->expectedName));
    }

    public function setExpectedContentTypeGroupName(string $expectedName)
    {
        $this->expectedName = $expectedName;
        $groups = $this->contentTypeService->loadContentTypeGroups();

        foreach ($groups as $group) {
            if ($group->identifier === $expectedName) {
                $this->contentTypeGroupId = $group->id;

                return;
            }
        }
    }

    public function getName(): string
    {
        return 'Content Type group';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('createButton', '.ez-icon-create'),
            new VisibleCSSLocator('listHeader', '.ez-table-header .ez-table-header__headline, header .ez-table__headline, header h5'),
            new VisibleCSSLocator('tableContainer', '.ez-container'),
            new VisibleCSSLocator('deleteButton', '.ez-icon-trash,button[data-original-title^="Delete"]'),
            new VisibleCSSLocator('tableItem', '.ez-main-container tbody tr'),
        ];
    }
}
