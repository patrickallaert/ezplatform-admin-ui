<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DraftConflictDialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentViewPage;
use PHPUnit\Framework\Assert;

class ContentViewContext implements Context
{
    private $argumentParser;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentViewPage */
    private $contentViewPage;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\LeftMenu */
    private $leftMenu;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\DraftConflictDialog */
    private $draftConflictDialog;

    public function __construct(
        ArgumentParser $argumentParser,
        ContentViewPage $contentViewPage,
        LeftMenu $leftMenu,
        UniversalDiscoveryWidget $universalDiscoveryWidget,
        DraftConflictDialog $draftConflictDialog)
    {
        $this->argumentParser = $argumentParser;
        $this->contentViewPage = $contentViewPage;
        $this->leftMenu = $leftMenu;
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->draftConflictDialog = $draftConflictDialog;
    }

    /**
     * @Given I start creating a new Content :contentType
     */
    public function startCreatingContent(string $contentType): void
    {
        $this->contentViewPage->startCreatingContent($contentType);
    }

    /**
     * @Given I start creating a new User
     */
    public function startCreatingUser(): void
    {
        $this->contentViewPage->startCreatingUser();
    }

    /**
     * @Given I start editing the current content
     * @Given I start editing the current content in :language language
     */
    public function startEditingContent(string $language = null): void
    {
        $this->contentViewPage->editContent($language);
    }

    /**
     * @Given I open UDW and go to :itemPath
     */
    public function iOpenUDWAndGoTo(string $itemPath): void
    {
        $this->leftMenu->verifyIsLoaded();
        $this->leftMenu->browse();

        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($this->argumentParser->replaceRootKeyword($itemPath));
        $this->universalDiscoveryWidget->openPreview();
    }

    /**
     * @Then there's a :itemName :itemType on Subitems list
     */
    public function verifyThereIsItemInSubItemList(string $itemName, string $itemType): void
    {
        Assert::assertTrue($this->contentViewPage->isChildElementPresent(['Name' => $itemName, 'Content type' => $itemType]));
    }

    /**
     * @Then there's no :itemName :itemType on Subitems list
     */
    public function verifyThereIsNoItemInSubItemListInRoot(string $itemName, string $itemType): void
    {
        Assert::assertFalse($this->contentViewPage->isChildElementPresent(['Name' => $itemName, 'Content type' => $itemType]));
    }

    /**
     * @Then content attributes equal
     */
    public function contentAttributesEqual(TableNode $parameters): void
    {
        foreach ($parameters->getHash() as $fieldData) {
            $fieldLabel = $fieldData['label'];
            $fieldTypeIdentifier = $fieldData['fieldTypeIdentifier'] ?? null;
            $expectedFieldValues = $fieldData;
            $this->contentViewPage->verifyFieldHasValues($fieldLabel, $expectedFieldValues, $fieldTypeIdentifier);
        }
    }

    /**
     * @When I start creating new draft from draft conflict modal
     */
    public function startCreatingNewDraftFromDraftConflictModal(): void
    {
        $this->draftConflictDialog->verifyIsLoaded();
        $this->draftConflictDialog->createNewDraft();
    }

    /**
     * @When I start editing draft with version number :versionNumber from draft conflict modal
     */
    public function startEditingDraftFromDraftConflictModal(string $versionNumber): void
    {
        $this->draftConflictDialog->verifyIsLoaded();
        $this->draftConflictDialog->edit($versionNumber);
    }

    /**
     * @When I send content to trash
     */
    public function iSendContentToTrash(): void
    {
        $this->contentViewPage->sendToTrash();
    }
}
