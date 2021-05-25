<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Table;

use EzSystems\Behat\Browser\Locator\LocatorCollection;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;

class SubitemsGrid extends Table
{
    private $minkParameters;

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('listElement', '.m-sub-items .c-grid-view-item'),
            new VisibleCSSLocator('parent', '.m-sub-items'),
        ];
    }

    public function isEmpty(): bool
    {
        return $this->getHTMLPage()->findAll($this->getLocator('listElement'))->any() === false;
    }

    public function hasElementOnCurrentPage(array $elementData): bool
    {
        $name = array_values($elementData);

        foreach ($this->getHTMLPage()->findAll($this->getLocator('listElement')) as $element) {
            if ($element->getText() === $name) {
                return true;
            }
        }

        return false;
    }

    public function getTableRow(array $elementData): TableRow
    {
        $name = array_values($elementData);

        foreach ($this->getHTMLPage()->findAll($this->getLocator('listElement')) as $element) {
            if ($element->getText() === $name) {
                return $this->tableFactory->createRow($element, new LocatorCollection([]));
            }
        }

        throw new \Exception('Table row with given data was not found!');
    }
}
