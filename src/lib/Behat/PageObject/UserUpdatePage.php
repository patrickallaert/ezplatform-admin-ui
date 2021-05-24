<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use Traversable;

class UserUpdatePage extends ContentUpdateItemPage
{
    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, RightMenu $rightMenu, Traversable $fieldTypeComponents)
    {
        parent::__construct($testEnv, $rightMenu, $fieldTypeComponents);
        $this->locators->replace(
            new VisibleCSSLocator(
                'formElement',
                '[name=ezplatform_content_forms_user_create],[name=ezplatform_content_forms_user_update]'
            )
        );
    }
}
