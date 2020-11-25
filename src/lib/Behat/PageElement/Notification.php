<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Exception;
use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Element\NodeElement;
use EzSystems\Behat\Browser\Locator\CSSLocator;
use PHPUnit\Framework\Assert;

/** Element that describes user notification bar, that appears on the bottom of the screen */
class Notification extends Component
{
    public function verifyAlertSuccess(): void
    {
        Assert::assertTrue(
            $this->getHTMLPage()
                ->setTimeout(20)
                ->find($this->getLocator('successAlert'))
                ->isVisible(),
            'Success alert not found.'
        );
    }

    public function verifyAlertFailure(): void
    {
        Assert::assertTrue(
            $this->getHTMLPage()
                ->setTimeout(20)
                ->find($this->getLocator('failureAlert'))
                ->isVisible(),
            'Failure alert not found.'
        );
    }

    public function getMessage(): string
    {
        return $this->getHTMLPage()->find($this->getLocator('alertMessage'))->getText();
    }

    public function closeAlert(): void
    {
        $alerts = $this->getHTMLPage()->findAll($this->getLocator('alert'));

        if ($alerts->any()) {
            $alerts->single()->click();
        }
    }

    public function isVisible(): bool
    {
        $elements =  $this->getHTMLPage()->findAll($this->getLocator('alert'));

        return $elements->any() ? $elements->single()->isVisible() : false;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertTrue(
            $this
                ->getHTMLPage()
                ->find($this->getLocator('alert'))
                ->isVisible()
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('alert', '.ez-notifications-container .alert.show'),
            new CSSLocator('alertMessage', '.ez-notifications-container .alert.show span:nth-of-type(2)'),
            new CSSLocator('successAlert', '.alert-success'),
            new CSSLocator('failureAlert', '.alert-danger'),
            new CSSLocator('closeAlert', 'button.close'),
        ];
    }
}
