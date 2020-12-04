<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BusinessContext;

use Behat\Behat\Context\Context;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu;
use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UserNotificationPopup;
use PHPUnit\Framework\Assert;

class UserNotificationContext implements Context
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UpperMenu */
    private $upperMenu;
    /**
     * @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UserNotificationPopup
     */
    private $userNotificationPopup;

    public function __construct(UpperMenu $upperMenu, UserNotificationPopup $userNotificationPopup)
    {
        $this->upperMenu = $upperMenu;
        $this->userNotificationPopup = $userNotificationPopup;
    }

    /**
     * @Given there is an unread notification for current user
     */
    public function thereIsNotificationForCurrentUser()
    {
        Assert::assertGreaterThan(0, $this->upperMenu->getNotificationsCount());
    }

    /**
     * @Given I go to user notification with details:
     */
    public function iGoToUserNotificationWithDetails(TableNode $notificationDetails)
    {
        $type = $notificationDetails->getHash()[0]['Type'];
        $description = $notificationDetails->getHash()[0]['Description'];

        $this->userNotificationPopup->verifyIsLoaded();
        $this->userNotificationPopup->clickNotification($type, $description);
    }
}
