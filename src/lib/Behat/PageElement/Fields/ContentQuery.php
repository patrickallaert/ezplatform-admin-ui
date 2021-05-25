<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\CssLocatorBuilder;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class ContentQuery extends NonEditableField
{
    public const ELEMENT_NAME = 'Content query';

    public function verifyValueInItemView(array $values): void
    {
        $expecteditems = explode(',', $values['value']);
        $actualItems = $this->getValueInItemView();
        $commonItems = array_intersect($expecteditems, $actualItems);

        Assert::assertEquals([], array_diff($expecteditems, $commonItems));
    }

    private function getValueInItemView(): array
    {
        $itemSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('queryResultItem'))
            ->build();

        return $this->getHTMLPage()->findAll($itemSelector)->map(function (ElementInterface $element) {
            return $element->getText();
        });
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [new VisibleCSSLocator('queryResultItem', 'p a')],
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezcontentquery';
    }
}
