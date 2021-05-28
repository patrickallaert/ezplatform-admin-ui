<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use Exception;
use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

class UserNotificationPopup extends Component
{
    public function clickNotification(string $expectedType, string $expectedDescription)
    {
        $notifications = $this->getHTMLPage()->findAll($this->getLocator('notificationItem'));

        foreach ($notifications as $notification) {
            $type = $notification->find($this->getLocator('notificationType'))->getText();
            if ($type !== $expectedType) {
                continue;
            }

            $description = $notification->find($this->getLocator('notificationDescription'))->getText();
            if ($description !== $expectedDescription) {
                continue;
            }

            $notification->click();

            return;
        }

        throw new Exception(sprintf('Notification of type: %s with description: %d not found', $type, $description));
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('notificationsPopupTitle'))
            ->assert()->textContains('Notifications');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('notificationsPopupTitle', '#view-notifications .modal-title'),
            new VisibleCSSLocator('notificationItem', '.ez-notifications-modal__item'),
            new VisibleCSSLocator('notificationType', '.ez-notifications-modal__type'),
            new VisibleCSSLocator('notificationDescription', '.ez-notifications-modal__description'),
        ];
    }
}
