<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\XPathLocator;
use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use PHPUnit\Framework\Assert;

class AdminUpdateItemPage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu */
    protected $rightMenu;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, RightMenu $rightMenu)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->rightMenu = $rightMenu;
    }

    public function getFieldValue($label)
    {
        return $this->getField($label)->getValue();
    }

    protected function getRoute(): string
    {
        throw new \Exception('Update Page cannot be opened on its own!');
    }

    public function getName(): string
    {
        return 'Admin item update';
    }

    public function fillFieldWithValue(string $fieldName, $value): void
    {
        $this->getField($fieldName)->setValue($value);
    }

    public function clickButton(string $label): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('button'))
            ->getByText($label)
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->rightMenu->verifyIsLoaded();
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('formElement'))->isVisible());
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('formElement', '.form-group'),
            new VisibleCSSLocator('closeButton', '.ez-content-edit-container__close'),
            new VisibleCSSLocator('button', 'button'),
            new VisibleCSSLocator('field', '.form-group'),
            new VisibleCSSLocator('fieldInput', 'input'),
        ];
    }

    private function getField(string $fieldName): ElementInterface
    {
        return $this->getHTMLPage()
            ->findAll(new XPathLocator('input', '//label/..'))
            ->getByText($fieldName)
            ->find(new VisibleCSSLocator('input', 'input'));
    }
}
