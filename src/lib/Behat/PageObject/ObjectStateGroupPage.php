<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use eZ\Publish\API\Repository\Repository;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use PHPUnit\Framework\Assert;

class ObjectStateGroupPage extends Page
{
    /** @var string */
    protected $expectedObjectStateGroupName;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    private $dialog;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $attributes;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $objectStates;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var mixed */
    private $expectedObjectStateGroupId;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, Table $attributes, Table $objectStates, Dialog $dialog, Repository $repository)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->dialog = $dialog;
        $this->attributes = $attributes->withParentLocator($this->getLocator('propertiesTable'));
        $this->objectStates = $objectStates->withParentLocator($this->getLocator('objectStatesTable'));
        $this->repository = $repository;
    }

    public function editObjectState(string $itemName): void
    {
        $this->objectStates->getTableRow(['Object state name' => $itemName])->edit();
    }

    public function createObjectState(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }

    public function setExpectedObjectStateGroupName(string $objectStateGroupName): void
    {
        $this->expectedObjectStateGroupName = $objectStateGroupName;

        /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectStateGroup[] $objectStateGroups */
        $objectStateGroups = $this->repository->sudo(function (Repository $repository) {
            return $this->repository->getObjectStateService()->loadObjectStateGroups();
        });

        foreach ($objectStateGroups as $objectStateGroup) {
            if ($objectStateGroup->getName() === $objectStateGroupName) {
                $this->expectedObjectStateGroupId = $objectStateGroup->id;
            }
        }
    }

    public function hasObjectStates(): bool
    {
        return count($this->objectStates->getColumnValues(['Object state name'])) > 0;
    }

    public function hasAttribute($label, $value): bool
    {
        return $this->attributes->hasElement([$label => $value]);
    }

    public function hasObjectState(string $objectStateName): bool
    {
        return $this->objectStates->hasElement(['Object state name' => $objectStateName]);
    }

    public function deleteObjectState(string $objectStateName)
    {
        $this->objectStates->getTableRow(['Object state name' => $objectStateName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function edit()
    {
        $this->attributes->getTableRowByIndex(0)->edit();
    }

    protected function getRoute(): string
    {
        return sprintf('/state/group/%d', $this->expectedObjectStateGroupId);
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            sprintf('Object state group: %s', $this->expectedObjectStateGroupName),
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    public function getName(): string
    {
        return 'Object state group';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('propertiesTable', '.ez-container:nth-of-type(1)'),
            new VisibleCSSLocator('objectStatesTable', '.ez-container:nth-of-type(2)'),
            new VisibleCSSLocator('createButton', '.ez-icon-create'),
            new VisibleCSSLocator('deleteButton', '.ez-icon-trash'),
        ];
    }
}
