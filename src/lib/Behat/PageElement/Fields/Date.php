<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\DateAndTimePopup;
use PHPUnit\Framework\Assert;

class Date extends FieldTypeComponent
{
    private const DATE_FORMAT = 'm/d/Y';

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\DateAndTimePopup */
    private $dateAndTimePopup;

    public function __construct(Session $session, MinkParameters $minkParameters, DateAndTimePopup $dateAndTimePopup)
    {
        parent::__construct($session, $minkParameters);
        $this->dateAndTimePopup = $dateAndTimePopup;
    }

    public function setValue(array $parameters): void
    {
        $fieldSelector = $this->parentLocator->withDescendant($this->getLocator('fieldInput'));

        $this->getHTMLPage()->find($fieldSelector)->click();
        $this->dateAndTimePopup->setDate(date_create($parameters['value']), self::DATE_FORMAT);
    }

    public function getValue(): array
    {
        $fieldSelector = $this->parentLocator->withDescendant($this->getLocator('fieldInput'));
        $value = $this->getHTMLPage()->find($fieldSelector)->getText();

        return [$value];
    }

    public function verifyValueInItemView(array $values): void
    {
        $expectedDateTime = date_create($values['value']);
        $actualDateTime = date_create($this->getHTMLPage()->find($this->parentLocator)->getText());
        Assert::assertEquals(
            $expectedDateTime,
            $actualDateTime,
            'Field has wrong value'
        );
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('fieldInput', 'input.flatpickr-input.ez-data-source__input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezdate';
    }
}
