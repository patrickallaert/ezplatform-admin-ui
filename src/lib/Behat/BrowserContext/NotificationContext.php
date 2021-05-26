<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use EzSystems\Behat\Core\Behat\ArgumentParser;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification;
use PHPUnit\Framework\Assert;

/** Context for actions on notifications */
class NotificationContext implements Context
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification */
    private $notification;

    /** @var \EzSystems\Behat\Core\Behat\ArgumentParser */
    private $argumentParser;

    public function __construct(Notification $notification, ArgumentParser $argumentParser)
    {
        $this->notification = $notification;
        $this->argumentParser = $argumentParser;
    }

    /**
     * @Then notification that :itemType :itemName is :action appears
     */
    public function notificationAppears(string $itemType, string $itemName, string $action): void
    {
        $expectedMessage = sprintf('%s \'%s\' %s.', $itemType, $itemName, $action);

        $this->notification->verifyIsLoaded();
        $this->notification->verifyAlertSuccess();
        Assert::assertEquals($expectedMessage, $this->notification->getMessage());
        $this->notification->closeAlert();
    }

    /**
     * @Then success notification that :message appears
     */
    public function specificNotificationAppears(string $message): void
    {
        $this->notification->verifyIsLoaded();
        $this->notification->verifyAlertSuccess();
        $this->notification->verifyMessage($message);
        $this->notification->closeAlert();
    }

    /**
     * @Then error notification that :message appears
     */
    public function specificErrorNotificationAppears(string $message): void
    {
        $this->notification->verifyIsLoaded();
        $this->notification->verifyAlertFailure();
        Assert::assertContains($message, $this->notification->getMessage());
        $this->notification->closeAlert();
    }
}
