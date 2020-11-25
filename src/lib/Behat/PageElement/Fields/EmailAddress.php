<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Locator\CSSLocator;

class EmailAddress extends FieldTypeComponent
{
    public function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezemail';
    }
}
