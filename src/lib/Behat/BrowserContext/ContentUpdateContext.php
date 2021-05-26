<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\NonEditableField;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\UserUpdatePage;
use PHPUnit\Framework\Assert;

class ContentUpdateContext implements Context
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\UserUpdatePage */
    private $userUpdatePage;

    public function __construct(ContentUpdateItemPage $contentUpdateItemPage, UserUpdatePage $userUpdatePage)
    {
        $this->contentUpdateItemPage = $contentUpdateItemPage;
        $this->userUpdatePage = $userUpdatePage;
    }

    /**
     * @When I set content fields
     */
    public function iSetFields(TableNode $table): void
    {
        $this->contentUpdateItemPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $this->filterOutNonEmptyValues($row);
            $this->contentUpdateItemPage->fillFieldWithValue($row['label'], $values);
        }
    }

    /**
     * @Given the :fieldName field is noneditable
     */
    public function verifyFieldIsNotEditable(string $fieldName): void
    {
        $field = $this->contentUpdateItemPage->getField($fieldName);
        Assert::assertEquals(NonEditableField::EXPECTED_NON_EDITABLE_TEXT, $field->getValue()[0]);
    }

    /**
     * @When I set content fields for user
     */
    public function iSetFieldsForUser(TableNode $table): void
    {
        $this->userUpdatePage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $this->filterOutNonEmptyValues($row);
            $this->userUpdatePage->fillFieldWithValue($row['label'], $values);
        }
    }

    /**
     * @When I select :contentPath from Image Asset Repository for :fieldName field
     */
    public function selectContentFromIARepository(string $contentPath, string $fieldName): void
    {
        $this->contentUpdateItemPage->getField($fieldName)->selectFromRepository($contentPath);
    }

    private function filterOutNonEmptyValues(array $parameters): array
    {
        $values = $parameters;
        unset($values['label']);

        return array_filter($values, function ($element) { return !empty($element) || $element === 0;});
    }

    /**
     * @Then content fields are set
     */
    public function verifyFieldsAreSet(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->contentUpdateItemPage->verifyFieldHasValue($row['label'], $row);
        }
    }

    /**
     * @When I click on the close button
     */
    public function iClickCloseButton(): void
    {
        $this->contentUpdateItemPage->close();
    }
}
