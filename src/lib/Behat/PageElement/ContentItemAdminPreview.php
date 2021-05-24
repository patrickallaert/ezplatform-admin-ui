<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Locator\CSSLocator;
use EzSystems\Behat\Browser\Page\TestEnvironment;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Traversable;

class ContentItemAdminPreview extends Component
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields\FieldTypeComponentInterface[] */
    private $fieldTypeComponents;

    public function __construct(TestEnvironment $testEnv, Traversable $fieldTypeComponents)
    {
        parent::__construct($testEnv);
        $this->fieldTypeComponents = iterator_to_array($fieldTypeComponents);
    }

    public function verifyFieldHasValues(string $fieldLabel, array $expectedValues, ?string $fieldTypeIdentifier)
    {
        $fieldPosition = $this->getFieldPosition($fieldLabel);
        $nthFieldLocator = new VisibleCSSLocator('', sprintf($this->getLocator('nthFieldContainer')->getSelector(), $fieldPosition));

        $fieldValueLocator = $nthFieldLocator->withDescendant($this->getLocator('fieldValue'));
        $fieldTypeIdentifier = $fieldTypeIdentifier ?? $this->detectFieldTypeIdentifier($fieldValueLocator);

        foreach ($this->fieldTypeComponents as $fieldTypeComponent) {
            if ($fieldTypeComponent->getFieldTypeIdentifier() === $fieldTypeIdentifier) {
                $fieldTypeComponent->setParentLocator($fieldValueLocator);
                $fieldTypeComponent->verifyValueInItemView($expectedValues);

                return;
            }
        }
    }

    private function getFieldPosition(string $fieldLabel): int
    {
        $searchText = sprintf('%s:', $fieldLabel);

        $fields = $this->getHTMLPage()->findAll($this->getLocator('fieldName'));

        $fieldPosition = 1;
        foreach ($fields as $field) {
            if ($field->getText() === $searchText) {
                return $fieldPosition;
            }

            ++$fieldPosition;
        }
    }

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('nthFieldContainer', 'div.ez-content-field:nth-of-type(%s)'),
            new VisibleCSSLocator('fieldName', '.ez-content-field-name'),
            new VisibleCSSLocator('fieldValue', '.ez-content-field-value'),
            new VisibleCSSLocator('fieldValueContainer', ':first-child'),
        ];
    }

    private function detectFieldTypeIdentifier(CSSLocator $fieldValueLocator)
    {
        $fieldClass = $this->getHTMLPage()
            ->find($fieldValueLocator->withDescendant($this->getLocator('fieldValueContainer')))
            ->getAttribute('class');


        if ($fieldClass === 'ez-scrollable-table-wrapper mb-0') {
            return 'ezuser';
        }

        if (strpos($fieldClass, 'ez-scrollable-table-wrapper') !== false) {
            return 'ezmatrix';
        }

        if ($fieldClass === '') {
            return 'ezboolean';
        }

        $fieldTypeIdentifierRegex = '/ez[a-z]*-field/';
        preg_match($fieldTypeIdentifierRegex, $fieldClass, $matches);

        return explode('-', $matches[0])[0];
    }
}
