<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Page\Browser;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DateAndTimePopup;
use PHPUnit\Framework\Assert;

class DateAndTime extends FieldTypeComponent
{
    private const VIEW_DATE_TIME_FORMAT = 'n/j/y, g:i A';

    private const FIELD_DISPLAY_FORMAT = 'd/m/Y G:i';
    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\DateAndTimePopup
     */
    private $dateAndTimePopup;

    public function __construct(Browser $browser, DateAndTimePopup $dateAndTimePopup)
    {
        parent::__construct($browser);
        $this->dateAndTimePopup = $dateAndTimePopup;
    }

    public function setValue(array $parameters): void
    {
        $fieldSelector = $this->parentLocator->withDescendant($this->getLocator('fieldInput'));
        $this->getHTMLPage()->find($fieldSelector)->click();

        $time = explode(':', $parameters['time']);

        $this->dateAndTimePopup->setDate(date_create($parameters['date']));
        $this->dateAndTimePopup->setTime($time[0], $time[1]);

        // This click is closing the date and time picker, to finally ensure that value is set up.
        $this->getHTMLPage()->find($this->parentLocator)->click();

        $expectedDateAndTimeValue = date_format(date_create(sprintf('%s, %s', $parameters['date'], $parameters['time'])), self::VIEW_DATE_TIME_FORMAT);
        $currentFieldValue = $this->getHTMLPage()->find($fieldSelector)->getValue();
        $actualTimeValue = date_format(date_create_from_format(self::FIELD_DISPLAY_FORMAT, $currentFieldValue), self::VIEW_DATE_TIME_FORMAT);

        Assert::assertEquals($expectedDateAndTimeValue, $actualTimeValue);
    }

    public function getValue(): array
    {
        $fieldSelector = $this->parentLocator->withDescendant($this->getLocator('fieldInput'));
        $value = $this->getHTMLPage()->find($fieldSelector)->getText();

        return [$value];
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedDate = date_format(date_create(sprintf('%s, %s', $values['date'], $values['time'])), self::VIEW_DATE_TIME_FORMAT);
        $actualDate = date_format(date_create($this->getHTMLPage()->find($this->parentLocator)->getText()), self::VIEW_DATE_TIME_FORMAT);
        Assert::assertEquals(
            $expectedDate,
            $actualDate,
            'Field has wrong value'
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezdatetime';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', '.flatpickr-input.ez-data-source__input'),
        ];
    }
}
