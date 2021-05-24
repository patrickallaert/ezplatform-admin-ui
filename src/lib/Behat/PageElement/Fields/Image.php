<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Locator\CSSLocator;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class Image extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $fieldSelector = $this->getLocator('fieldInput')->withParent($this->parentLocator);

        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->getRemoteFileUploadPath($parameters['value'])
        );

        $alternativeText = str_replace('.zip', '', $parameters['value']);

        $this->getHTMLPage()
            ->find($this->parentLocator->withDescendant($this->getLocator('alternativeText')))
            ->setValue($alternativeText);
    }

    public function verifyValueInItemView(array $values): void
    {
        $filename = str_replace('.zip', '', $values['value']);

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Image has wrong file name'
        );

        $fileFieldSelector = $this->parentLocator->withDescendant($this->getLocator('image'));

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->setTimeout(5)->find($fileFieldSelector)->getAttribute('src'),
            'Image has wrong source'
        );
    }

    public function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input[type=file]'),
            new VisibleCSSLocator('image', '.ez-field-preview__image-wrapper .ez-field-preview__image img'),
            new VisibleCSSLocator('alternativeText', 'input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezimage';
    }
}
