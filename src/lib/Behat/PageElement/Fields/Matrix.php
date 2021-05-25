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
use EzSystems\Behat\Browser\Locator\LocatorInterface;
use PHPUnit\Framework\Assert;

class Matrix extends FieldTypeComponent
{
    public function setValue(array $parameters): void
    {
        $matrixValues = $this->parseParameters($parameters);

        $availableRows = count($this->getHTMLPage()->findAll($this->getLocator('row')));
        $rowsToSet = count($matrixValues);

        if ($rowsToSet > $availableRows) {
            $this->addRows($rowsToSet - $availableRows);
        }

        foreach ($matrixValues as $rowIndex => $row) {
            foreach ($row as $column => $value) {
                $this->internalSetValue((int)$rowIndex, $column, $value);
            }
        }
    }

    public function getValue(): array
    {
        return [$this->getParsedTableValue($this->getLocator('editModeTableHeaders'), $this->getLocator('editModeTableRow'))];
    }

    public function verifyValueInItemView(array $expectedValue): void
    {
        $parsedTable = $this->getParsedTableValue($this->getLocator('viewModeTableHeaders'), $this->getLocator('viewModeTableRow'));

        Assert::assertEquals($expectedValue['value'], $parsedTable);
    }

    private function parseParameters(array $parameters): array
    {
        $rows = explode(',', $parameters['value']);

        $columnIdentifiers = explode(':', array_shift($rows));
        $numberOfColumns = count($columnIdentifiers);

        $parsedRows = [];
        foreach ($rows as $row) {
            $parsedRow = [];
            $columnValues = explode(':', $row);
            for ($i = 0; $i < $numberOfColumns; ++$i) {
                $parsedRow[$columnIdentifiers[$i]] = $columnValues[$i];
            }

            $parsedRows[] = $parsedRow;
        }

        return $parsedRows;
    }

    private function addRows(int $numberOfRows): void
    {
        for ($i = 0; $i < $numberOfRows; ++$i) {
            $this->getHTMLPage()->find($this->getLocator('addRowButton'))->click();
        }
    }

    private function internalSetValue(int $rowIndex, string $column, $value): void
    {
        $matrixCellSelector = CssLocatorBuilder::combine(
            $this->getLocator('matrixCellSelectorFormat')->getSelector(),
            new VisibleCSSLocator('rowIndex', (string)$rowIndex),
            new VisibleCSSLocator('columnIndex', $column),
        );

        $this->getHTMLPage()->find($matrixCellSelector)->setValue($value);
    }

    private function getParsedTableValue(LocatorInterface $headerSelector, LocatorInterface $rowSelector): string
    {
        $parsedTable = '';

        $headers = $this->getHTMLPage()->findAll($headerSelector)->map(function (ElementInterface $element) {
            return $element->getText();
        });

        $parsedTable .= implode(':', $headers);

        $rows = $this->getHTMLPage()->findAll($rowSelector);
        foreach ($rows as $row) {
            $parsedTable .= ',';
            $cellValues = $row
                ->findAll(new VisibleCSSLocator('cell', 'td'))
                ->map(function (ElementInterface $element) { return $element->getText();});
            $parsedTable .= implode(':', $cellValues);
        }

        return $parsedTable;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('matrixCellSelectorFormat', '[name="ezplatform_content_forms_content_edit[fieldsData][ezmatrix][value][entries][%d][%s]"]'),
            new VisibleCSSLocator('row', '.ez-table__matrix-entry'),
            new VisibleCSSLocator('addRowButton', '.ez-btn--add-matrix-entry'),
            new VisibleCSSLocator('viewModeTableHeaders', '.ez-content-field-value thead th'),
            new VisibleCSSLocator('viewModeTableRow', '.ez-content-field-value tbody tr'),
            new VisibleCSSLocator('editModeTableHeaders', '.ez-table thead th[data-identifier]'),
            new VisibleCSSLocator('editModeTableRow', '.ez-table tr.ez-table__matrix-entry'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezmatrix';
    }
}
