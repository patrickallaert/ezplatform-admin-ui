<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Page\Browser;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\FieldTypeComponent;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use PHPUnit\Framework\Assert;
use Traversable;

class ContentUpdateItemPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu */
    private $rightMenu;

    private $pageTitle;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\FieldTypeComponent[] */
    private $fieldTypeComponents;

    public function __construct(
        Browser $browser,
        RightMenu $rightMenu,
        Traversable $fieldTypeComponents
    ) {
        parent::__construct($browser);
        $this->rightMenu = $rightMenu;
        $this->fieldTypeComponents = iterator_to_array($fieldTypeComponents);
    }

    public function verifyIsLoaded(): void
    {
        if ($this->pageTitle !== null) {
            Assert::assertEquals(
                $this->pageTitle,
                $this->getHTMLPage()
                    ->setTimeout(10)
                    ->find($this->getLocator('pageTitle'))->getText()
            );
        }

        $this->rightMenu->verifyIsLoaded();
        $this->getHTMLPage()->setTimeout(10)->find($this->getLocator('formElement'))->assert()->isVisible();
    }

    public function setExpectedPageTitle(string $title)
    {
        $this->pageTitle = $title;
    }

    public function getName(): string
    {
        return 'Content Update';
    }

    public function fillFieldWithValue($label, array $value)
    {
        $this->getField($label)->setValue($value);
    }

    public function close()
    {
        $this->getHTMLPage()->find($this->getLocator('closeButton'))->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-content-edit-page-title__title'),
            new VisibleCSSLocator('formElement', '[name=ezplatform_content_forms_content_edit]'),
            new VisibleCSSLocator('closeButton', '.ez-content-edit-container__close'),
            new VisibleCSSLocator('fieldLabel', '.ez-field-edit__label-wrapper label.ez-field-edit__label, .ez-field-edit__label-wrapper legend, .ez-card > .card-body > div > div > legend'),
            new VisibleCSSLocator('nthField', '.ez-card .card-body > div > div:nth-of-type(%s)'),
            new VisibleCSSLocator('noneditableFieldClass', 'ez-field-edit--eznoneditable'),
            new VisibleCSSLocator('fieldOfType', '.ez-field-edit--%s'),
        ];
    }

    protected function getRoute(): string
    {
        throw new \Exception('This page cannot be opened on its own!');
    }

    public function getField(string $fieldName): FieldTypeComponent
    {
        $fieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthField')->getSelector(), $this->getFieldPosition($fieldName)));
        $fieldtypeIdentifier = $this->getFieldtypeIdentifier($fieldLocator, $fieldName);

        foreach ($this->fieldTypeComponents as $fieldTypeComponent) {
            if ($fieldTypeComponent->getFieldTypeIdentifier() === $fieldtypeIdentifier) {
                $fieldTypeComponent->setParentLocator($fieldLocator);

                return $fieldTypeComponent;
            }
        }
    }

    protected function getFieldPosition(string $fieldName): int
    {
        $fieldElements = $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('fieldLabel'));

        $foundFields = [];
        foreach ($fieldElements as $fieldPosition => $fieldElement) {
            $fieldText = $fieldElement->getText();
            $foundFields[] = $fieldText;
            if ($fieldText === $fieldName) {
                // +1 because CSS is 1-indexed and arrays are 0-indexed
                return $fieldPosition + 1;
            }
        }

        Assert::fail(sprintf('Field %s not found. Found: %s', $fieldName, implode(',', $foundFields)));
    }

    public function verifyFieldHasValue(string $label, array $fieldData): void
    {
        $this->getField($label)->verifyValueInEditView($fieldData);
    }

    private function getFieldtypeIdentifier(VisibleCSSLocator $fieldLocator, string $fieldName): string
    {
        $isEditable = !$this->getHTMLPage()
            ->find($fieldLocator)
            ->hasClass($this->getLocator('noneditableFieldClass')->getSelector());

        if (!$isEditable) {
            return strtolower($fieldName);
        }

        $fieldClass = $this->getHTMLPage()->find($fieldLocator)->getAttribute('class');
        preg_match('/ez-field-edit--ez[a-z]*/', $fieldClass, $matches);

        return explode('--', $matches[0])[1];
    }
}
