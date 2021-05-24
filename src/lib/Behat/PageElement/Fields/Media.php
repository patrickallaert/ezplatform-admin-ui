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

class Media extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $fieldSelector = $this->getLocator('fieldInput')->withParent($this->parentLocator);
        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->getRemoteFileUploadPath($parameters['value'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        $filename = str_replace('.zip', '', $values['value']);

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Media has wrong file name'
        );

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find(
                $this->parentLocator->withDescendant($this->getLocator('video'))
            )->getAttribute('src'),
            'Media has wrong source'
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input[type=file]'),
            new VisibleCSSLocator('video', 'video'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezmedia';
    }
}
