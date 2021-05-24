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
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use PHPUnit\Framework\Assert;

class ObjectStatePage extends Page
{
    /** @var string */
    private $expectedObjectStateName;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var mixed */
    private $expectedObjectStateId;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    public function __construct(Session $session, MinkParameters $minkParameters, Router $router, Repository $repository, Table $table)
    {
        parent::__construct($session, $minkParameters, $router);
        $this->repository = $repository;
        $this->table = $table;
    }

    public function hasAttribute($label, $value)
    {
        return $this->table->hasElement([$label => $value]);
    }

    public function edit()
    {
        $this->getHTMLPage()->find($this->getLocator('editButton'))->click();
    }

    protected function getRoute(): string
    {
        return sprintf('/state/state/%s', $this->expectedObjectStateId);
    }

    public function getName(): string
    {
        return 'Object state';
    }

    public function setExpectedObjectStateName(string $objectStateName)
    {
        $this->expectedObjectStateName = $objectStateName;

        /** @var \eZ\Publish\API\Repository\Values\ObjectState\ObjectState $expectedObjectState */
        $expectedObjectState = $this->repository->sudo(function (Repository $repository) use ($objectStateName) {
            foreach ($repository->getObjectStateService()->loadObjectStateGroups() as $group) {
                foreach ($repository->getObjectStateService()->loadObjectStates($group) as $objectState) {
                    if ($objectState->getName() === $objectStateName) {
                        return $objectState;
                    }
                }
            }
        });

        $this->expectedObjectStateId = $expectedObjectState->id;
    }

    public function verifyIsLoaded(): void
    {
        Assert::assertEquals(
            sprintf('Object state: %s', $this->expectedObjectStateName),
            $this->getHTMLPage()->find($this->getLocator('pageTitle'))->getText()
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
            new VisibleCSSLocator('editButton', '.ez-icon-edit'),
        ];
    }
}
