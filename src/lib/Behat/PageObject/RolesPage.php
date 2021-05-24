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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use PHPUnit\Framework\Assert;

class RolesPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    private $dialog;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, Table $table, Dialog $dialog)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->table = $table;
        $this->dialog = $dialog;
    }

    public function verifyItemAttribute(string $label, string $value, string $itemName): void
    {
        Assert::assertEquals(
            $value,
            $this->adminList->table->getTableCellValue($itemName, $label),
            sprintf('Attribute "%s" of item "%s" has wrong value.', $label, $itemName)
        );
    }

    public function create(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function isRoleOnTheList(string $roleName): bool
    {
        return $this->table->hasElement(['Name' => $roleName]);
    }

    public function editRole(string $roleName): void
    {
        $this->table->getTableRow(['Name' => $roleName])->edit();
    }

    public function startAssinging(string $roleName): void
    {
        $this->table->getTableRow(['Name' => $roleName])->assign();
    }

    public function deleteRole(string $roleName)
    {
        $this->table->getTableRow(['Name' => $roleName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteRoleButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    protected function getRoute(): string
    {
        return '/role/list';
    }

    public function getName(): string
    {
        return 'Roles';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Roles',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createButton', '.ez-icon-create'),
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('deleteRoleButton', '#delete-roles'),
        ];
    }
}
