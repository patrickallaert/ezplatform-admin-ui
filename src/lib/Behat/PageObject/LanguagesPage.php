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

class LanguagesPage extends Page
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

    public function editLanguage(string $languageName): void
    {
        $this->table->getTableRow(['Name' => $languageName])->edit();
    }

    public function create(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function deleteLanguage(string $languageName): void
    {
        $this->table->getTableRow(['Name' => $languageName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function isLanguageOnTheList(string $languageName)
    {
        return $this->table->hasElement(['Name' => $languageName]);
    }

    protected function getRoute(): string
    {
        return 'language/list';
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            'Languages',
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
        Assert::assertEquals(
            'Languages',
            $this->getHTMLPage()->find($this->getLocator('listHeader'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Languages';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('listHeader', '.ez-table-header .ez-table-header__headline, header .ez-table__headline, header h5'),
            new VisibleCSSLocator('createButton', '.ez-icon-create'),
            new VisibleCSSLocator('deleteButton', '.ez-icon-trash,button[data-original-title^="Delete"]'),
        ];
    }
}
