<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use eZ\Publish\API\Repository\Repository;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use PHPUnit\Framework\Assert;

class LanguagePage extends Page
{
    /** @var string */
    private $expectedLanguageName;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    private $dialog;

    /** @var int */
    private $expectedLanguageId;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    public function __construct(Session $session, Router $router, Table $table, Dialog $dialog, Repository $repository)
    {
        parent::__construct($session, $router);
        $this->table = $table;
        $this->dialog = $dialog;
        $this->repository = $repository;
    }

    public function delete()
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function hasProperties($data): bool
    {
        $hasExpectedEnabledFieldValue = true;
        if (array_key_exists('Enabled', $data)) {
            // Table does not handle returning non-string values
            $hasEnabledField = $this->getHTMLPage()->find($this->getLocator('enabledField'))->hasAttribute('checked');
            $shouldHaveEnabledField = $data['Enabled'] === 'true';
            $hasExpectedEnabledFieldValue = $hasEnabledField === $shouldHaveEnabledField;
            unset($data['Enabled']);
        }

        return $hasExpectedEnabledFieldValue && $this->table->hasElement($data);
    }

    public function edit()
    {
        $this->getHTMLPage()->find($this->getLocator('editButton'))->click();
    }

    protected function getRoute(): string
    {
        return sprintf('/language/view/%d', $this->expectedLanguageId);
    }

    public function getName(): string
    {
        return 'Language';
    }

    public function setExpectedLanguageName(string $languageName)
    {
        $this->expectedLanguageName = $languageName;

        $languages = $this->repository->sudo(function (Repository $repository) {
            return $repository->getContentLanguageService()->loadLanguages();
        });

        foreach ($languages as $language) {
            if ($language->name === $languageName) {
                $this->expectedLanguageId = $language->id;

                return;
            }
        }
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            sprintf('Language "%s"', $this->expectedLanguageName),
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('deleteButton', 'button[data-original-title="Delete language"]'),
            new VisibleCSSLocator('editButton', '[data-original-title="Edit"]'),
            new VisibleCSSLocator('enabledField', 'input[data-original-title="Enabled"]'),
        ];
    }
}
