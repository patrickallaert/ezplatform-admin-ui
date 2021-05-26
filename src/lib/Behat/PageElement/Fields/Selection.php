<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Locator\CssLocatorBuilder;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;

class Selection extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $value = $parameters['value'];

        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectBar'))
            ->build();

        $this->getHTMLPage()->find($fieldSelector)->click();
        $this->getHTMLPage()->findAll($this->getLocator('selectOption'))->getByText($value)->click();
    }

    public function getValue(): array
    {
        $fieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('selectBar'))
            ->build();

        return [$this->getHTMLPage()->find($fieldSelector)->getValue()];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezselection';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('selectBar', '.ez-custom-dropdown__selection-info'),
            new VisibleCSSLocator('selectOption', '.ez-custom-dropdown__item'),
            new VisibleCSSLocator('specificOption', '.ez-custom-dropdown__item:nth-child(%s)'),
        ];
    }
}
