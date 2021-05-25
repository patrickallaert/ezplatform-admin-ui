<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use eZ\Publish\API\Repository\ContentTypeService;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;

class ContentTypePage extends Page
{
    /** @var string */
    private $expectedContentTypeName;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var mixed */
    private $expectedContenTypeGroupId;

    /** @var mixed */
    private $expectedContenTypeId;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $contentTypeDataTable;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $globalPropertiesTable;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $fieldTable;

    public function __construct(
        Session $session,
        MinkParameters $minkParameters,
        Router $router,
        ContentTypeService $contentTypeService,
        Table $contentTypeDataTable,
        Table $fieldTable)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeDataTable = $contentTypeDataTable->withParentLocator($this->getLocator('contentTypeDataTable'));
        $this->fieldTable = $fieldTable->withParentLocator($this->getLocator('contentFieldsTable'));
    }

    public function hasProperty($label, $value): bool
    {
        if (in_array($label, ['Name', 'Identifier', 'Description'])) {
            return $this->contentTypeDataTable->hasElement([$label => $value]);
        }

        return $this->getHTMLPage()
            ->findAll($this->getLocator('globalPropertiesRow'))
            ->getByChildElementText($this->getLocator('globalPropertiesLabel'), $label)
            ->find($this->getLocator('globalPropertiesValue'))
            ->getText() === $value;
    }

    public function hasFieldType(array $fieldTypeData): bool
    {
        return $this->fieldTable->hasElement($fieldTypeData);
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/contenttypegroup/%d/contenttype/%d',
            $this->expectedContenTypeGroupId, $this->expectedContenTypeId
        );
    }

    public function getName(): string
    {
        return 'Content Type';
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals($this->expectedContentTypeName);
    }

    public function setExpectedContentTypeName(string $contentTypeName): void
    {
        $this->expectedContentTypeName = $contentTypeName;

        foreach ($this->contentTypeService->loadContentTypeGroups() as $group) {
            foreach ($this->contentTypeService->loadContentTypes($group) as $contentType) {
                if ($contentType->getName() === $contentTypeName) {
                    $this->expectedContenTypeId = $contentType->id;
                    $this->expectedContenTypeGroupId = $group->id;

                    return;
                }
            }
        }
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createButton', '.btn-icon .ez-icon-create'),
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('contentTypeDataTable', '.ez-fieldgroup .ez-fieldgroup__content .ez-table'),
            new VisibleCSSLocator('contentFieldsTable', '.ez-fieldgroup:nth-of-type(2)'),
            new VisibleCSSLocator('globalPropertiesRow', '.ez-fieldgroup__content .ez-table__row'),
            new VisibleCSSLocator('globalPropertiesLabel', '.ez-table__cell:nth-of-type(1)'),
            new VisibleCSSLocator('globalPropertiesValue', '.ez-table__cell:nth-of-type(2)'),
        ];
    }
}
