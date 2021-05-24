<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use EzSystems\Behat\API\ContentData\FieldTypeNameConverter;
use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\RightMenu;

class ContentTypeUpdatePage extends AdminUpdateItemPage
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Notification */
    private $notification;

    public function __construct(
        Session $session,
        MinkParameters $minkParameters,
        Router $router,
        RightMenu $rightMenu,
        Notification $notification
    )
    {
        parent::__construct($session, $minkParameters, $router, $rightMenu);
        $this->notification = $notification;
    }

    public function fillFieldDefinitionFieldWithValue(string $fieldName, string $label, string $value)
    {
        $this->expandFieldDefinition($fieldName);

        $this->getFieldDefinition($fieldName)
            ->findAll($this->getLocator('field'))->getByText($label)
            ->find($this->getLocator('fieldInput'))->setValue($value);
    }

    public function expandFieldDefinition(string $fieldName): void
    {
        $fieldDefinition = $this->getFieldDefinition($fieldName);

        if ($fieldDefinition->hasClass($this->getLocator('fieldCollapsed')->getSelector())) {
            $fieldDefinition->find($this->getLocator('fieldDefinitionToggler'))->click();
        }
    }

    public function specifyLocators(): array
    {
        return array_merge(parent::specifyLocators(), [
            new VisibleCSSLocator('fieldTypesList', '#ezplatform_content_forms_contenttype_update_fieldTypeSelection'),
            new VisibleCSSLocator('addFieldDefinition', '#ezplatform_content_forms_contenttype_update_addFieldDefinition'),
            new VisibleCSSLocator('fieldDefinitionContainer', '.ez-card--toggle-group'),
            new VisibleCSSLocator('fieldDefinitionName', '.ez-card--toggle-group .ez-card__header .form-check-label'),
            new VisibleCSSLocator('fieldBody', 'ez-card__body'),
            new VisibleCSSLocator('fieldCollapsed', 'ez-card--collapsed'),
            new VisibleCSSLocator('fieldDefinitionToggler', '.ez-card__body-display-toggler'),
        ]);
    }

    public function addFieldDefinition(string $fieldName)
    {
        $this->getHTMLPage()->find($this->getLocator('fieldTypesList'))->selectOption($fieldName);
        $this->getHTMLPage()->find($this->getLocator('addFieldDefinition'))->click();
        $this->getFieldDefinition($fieldName)->assert()->isVisible();

        $this->notification->verifyIsLoaded();
        $this->notification->verifyAlertSuccess();
        $this->notification->closeAlert();
    }

    private function getFieldDefinition($fieldName): ElementInterface
    {
        $fieldTypeIdentifier = FieldTypeNameConverter::getFieldTypeIdentifierByName($fieldName);

        return $this->getHTMLPage()
            ->findAll($this->getLocator('fieldDefinitionContainer'))
            ->filter(function (ElementInterface $element) use ($fieldTypeIdentifier) {
                return strpos($element->find($this->getLocator('fieldDefinitionName'))->getText(), $fieldTypeIdentifier) !== false;
            })
            ->first();
    }
}
