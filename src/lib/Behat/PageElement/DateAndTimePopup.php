<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use DateTime;
use EzSystems\Behat\Browser\Component\Component;
use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class DateAndTimePopup extends Component
{
    private const DATETIME_FORMAT = 'd/m/Y';

    private const SETTING_SCRIPT_FORMAT = "document.querySelector('%s %s')._flatpickr.setDate('%s', true, '%s')";

    private const ADD_CALLBACK_TO_DATEPICKER_SCRIPT_FORMAT = 'var fi = document.querySelector(\'%s .flatpickr-input\');
                const onChangeOld = fi._flatpickr.config.onChange;
                const onChangeNew = (dates, dateString, flatpickInstance) => {
                flatpickInstance.input.classList.add("date-set");
            };
        if (onChangeOld instanceof Array) {
                fi._flatpickr.config.onChange = [...onChangeOld, onChangeNew];
        } else if (onChangeOld) {
                fi._flatpickr.config.onChange = [onChangeOld, onChangeNew];
            } else {
                fi._flatpickr.config.onChange = onChangeNew;
            }';

    /** @var \EzSystems\Behat\Browser\Locator\VisibleCSSLocator */
    private $parentLocator;

    public function __construct(Session $session, MinkParameters $minkParameters)
    {
        parent::__construct($session, $minkParameters);
        $this->parentLocator = VisibleCSSLocator::empty();
    }

    /**
     * @param \DateTime $date Date to set
     */
    public function setDate(DateTime $date, string $dateFormat = self::DATETIME_FORMAT): void
    {
        $this->getSession()->executeScript(
            sprintf(
                self::ADD_CALLBACK_TO_DATEPICKER_SCRIPT_FORMAT,
                $this->parentLocator->getSelector()
            ));

        $dateScript = sprintf(
            self::SETTING_SCRIPT_FORMAT,
            $this->parentLocator->getSelector(),
            $this->getLocator('flatpickrSelector')->getSelector(),
            $date->format($dateFormat),
            $dateFormat
        );
        $this->getSession()->getDriver()->executeScript($dateScript);

        Assert::assertTrue(
            $this->getHTMLPage()
                ->find($this->getLocator('dateSet'))
                ->isVisible()
        );
    }

    /**
     * @param string $hour Hour to set
     * @param string $minute Minute to set
     */
    public function setTime(string $hour, string $minute): void
    {
        $isTimeOnly = $this->getHTMLPage()->findAll($this->getLocator('timeOnly'))->any();

        if (!$isTimeOnly) {
            // get current date as it's not possible to set time without setting date
            $currentDateScript = sprintf('document.querySelector("%s %s")._flatpickr.selectedDates[0].toLocaleString()',
                $this->parentLocator->getSelector(),
                $this->getLocator('flatpickrSelector')->getSelector()
            );
            $currentDate = $this->getSession()->getDriver()->evaluateScript($currentDateScript);
        }

        $valueToSet = $isTimeOnly ? sprintf('%s:%s:00', $hour, $minute) : sprintf('%s, %s:%s:00', explode(',', $currentDate)[0], $hour, $minute);
        $format = $isTimeOnly ? 'H:i:S' : 'm/d/Y, H:i:S';

        $timeScript = sprintf(
            self::SETTING_SCRIPT_FORMAT,
            $this->parentLocator->getSelector(),
            $this->getLocator('flatpickrSelector')->getSelector(),
            $valueToSet,
            $format
        );

        $this->getSession()->getDriver()->executeScript($timeScript);
    }

    public function setParentLocator(VisibleCSSLocator $locator)
    {
        $this->parentLocator = $locator;
    }

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('calendarSelectorInline', '.flatpickr-calendar.inline'),
            new VisibleCSSLocator('calendarSelector', '.flatpickr-calendar'),
            new VisibleCSSLocator('flatpickrSelector', '.flatpickr-input'),
            new VisibleCSSLocator('dateSet', '.date-set'),
            new VisibleCSSLocator('timeOnly', '.flatpickr-calendar.noCalendar'),
        ];
    }
}
