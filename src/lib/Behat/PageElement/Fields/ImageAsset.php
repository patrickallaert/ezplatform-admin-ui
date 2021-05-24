<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class ImageAsset extends Image
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification */
    private $notification;

    public function __construct(Session $session, MinkParameters $minkParameters, UniversalDiscoveryWidget $universalDiscoveryWidget, Notification $notification)
    {
        parent::__construct($session, $minkParameters);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->notification = $notification;
    }

    private const IMAGE_ASSET_NOTIFICATION_MESSAGE = 'The image has been published and can now be reused';

    public function setValue(array $parameters): void
    {
        // close notification about new draft created successfully if it's still visible
        if ($this->notification->isVisible()) {
            $this->notification->verifyAlertSuccess();
            $this->notification->closeAlert();
        }

        $fieldSelector = $this->getLocator('fieldInput')->withParent($this->parentLocator);

        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->getRemoteFileUploadPath($parameters['value'])
        );

        $this->notification->verifyAlertSuccess();
        Assert::assertEquals(self::IMAGE_ASSET_NOTIFICATION_MESSAGE, $this->notification->getMessage());
    }

    public function selectFromRepository(string $path): void
    {
        $this->getHTMLPage()
            ->find($this->parentLocator->withDescendant($this->getLocator('selectFromRepoButton')))
            ->click();
        $this->universalDiscoveryWidget->verifyIsLoaded();
        $this->universalDiscoveryWidget->selectContent($path);
        $this->universalDiscoveryWidget->confirm();
    }

    public function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [new VisibleCSSLocator('selectFromRepoButton', '.ez-data-source__btn-select')]
        );
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezimageasset';
    }
}
