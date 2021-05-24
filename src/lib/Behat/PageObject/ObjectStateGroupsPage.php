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

class ObjectStateGroupsPage extends Page
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

    public function isObjectStateGroupOnTheList(string $objectStateGroupName): bool
    {
        return $this->table->hasElement(['Object state group name' => $objectStateGroupName]);
    }

    public function editObjectStateGroup(string $objectStateGroupName)
    {
        $this->table->getTableRow(['Object state group name' => $objectStateGroupName])->edit();
    }

    public function createObjectStateGroup()
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function deleteObjectStateGroup(string $objectStateGroupName)
    {
        $this->table->getTableRow(['Object state group name' => $objectStateGroupName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    protected function getRoute(): string
    {
        return '/state/groups';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Object state groups',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Object State groups';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('createButton', '.ez-icon-create'),
            new VisibleCSSLocator('deleteButton', '#delete-object-state-groups'),
        ];
    }
}
