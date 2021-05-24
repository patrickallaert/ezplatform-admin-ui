<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageObject;

use eZ\Publish\API\Repository\Repository;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use EzSystems\Behat\Browser\Routing\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\Behat\Browser\Page\Page;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\TableNavigationTab;
use PHPUnit\Framework\Assert;

class RolePage extends Page
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Dialog */
    public $dialog;

    /** @var string */
    private $expectedRoleName;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\TableNavigationTab */
    private $tableNavigationTab;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    private $expectedRoleId;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $policies;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $assignments;

    public function __construct(
        Session $session, MinkParameters $minkParameters, Router $router,
        TableNavigationTab $tableNavigationTab,
        Dialog $dialog,
        Repository $repository,
        Table $policies,
        Table $assignments
    ) {
        parent::__construct($session, $minkParameters, $router);
        $this->tableNavigationTab = $tableNavigationTab;
        $this->dialog = $dialog;
        $this->repository = $repository;
        $this->policies = $policies->withParentLocator($this->getLocator('policiesTable'));
        $this->assignments = $assignments->withParentLocator($this->getLocator('assignmentTable'));
    }

    /**
     * Verifies if Role with Limitation from given list is present.
     *
     * @param string $listName
     * @param string $moduleAndFunction
     * @param string $limitation
     *
     * @return bool
     */
    public function isRoleWithLimitationPresent(string $moduleAndFunction, string $limitation): bool
    {
        $this->tableNavigationTab->goToTab('Policies');
        $actualPoliciesList = $this->policies->getColumnValues(['Module', 'Function', 'Limitations']);

        [$expectedModule, $expectedFunction] = explode('/', $moduleAndFunction);

        foreach ($actualPoliciesList as $policy) {
            if (
                $policy['Module'] === $expectedModule &&
                $policy['Function'] === $expectedFunction &&
                $this->isLimitationCorrect($limitation, $policy['Limitations'])
            ) {
                return true;
            }
        }

        return false;
    }

    private function isLimitationCorrect(string $expectedLimitation, string $actualLimitations): bool
    {
        if ($expectedLimitation === 'None') {
            return $actualLimitations === 'None';
        }

        [$expectedLimitationType, $expectedLimitationValue] = explode(':', $expectedLimitation);
        $expectedLimitationValues = array_map(function (string $value) {
            return trim($value);
        }, explode(',', $expectedLimitationValue));

        $limitationTypePos = strpos($actualLimitations, $expectedLimitationType);
        $actualLimitationsStartingFromExpectedType = substr($actualLimitations, $limitationTypePos);

        $valuePositionsDictionary = [];

        foreach ($expectedLimitationValues as $value) {
            $position = strpos($actualLimitationsStartingFromExpectedType, $value);
            if ($position === false) {
                return false;
            }

            $valuePositionsDictionary[$position] = $value;
        }

        ksort($valuePositionsDictionary);
        $combinedExpectedLimitation = sprintf('%s: %s', $expectedLimitationType, implode(', ', $valuePositionsDictionary));

        return strpos($actualLimitations, $combinedExpectedLimitation) !== false;
    }

    public function setExpectedRoleName(string $roleName)
    {
        $this->expectedRoleName = $roleName;

        /** @var \eZ\Publish\API\Repository\Values\User\Role[] $roles */
        $roles = $this->repository->sudo(function (Repository $repository) {
            return $repository->getRoleService()->loadRoles();
        });

        foreach ($roles as $role) {
            if ($role->identifier === $roleName) {
                $this->expectedRoleId = $role->id;
                break;
            }
        }
    }

    public function goToTab(string $tabName)
    {
        $this->tableNavigationTab->goToTab($tabName);
    }

    public function getRoute(): string
    {
        return sprintf('/role/%d', $this->expectedRoleId);
    }

    public function getName(): string
    {
        return 'Role';
    }

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('assignUsersButton', '[data-original-title="Assign to Users/Groups"]'),
            new VisibleCSSLocator('deleteAssignmentButton', '#delete-role-assignments'),
            new VisibleCSSLocator('deletePoliciesButton', '#delete-policies'),
            new VisibleCSSLocator('createPolicyButton', '[data-original-title="Add a new Policy"]'),
            new VisibleCSSLocator('assignmentTable', '[name="role_assignments_delete"]'),
            new VisibleCSSLocator('policiesTable', '[name="policies_delete"]'),
            new VisibleCSSLocator('pageTitle', '.ez-header h1'),
        ];
    }

    public function verifyIsLoaded(): void
    {
        $this->tableNavigationTab->verifyIsLoaded();
        $this->getHTMLPage()
            ->find($this->getLocator('pageTitle'))
            ->assert()->textEquals(sprintf('Role "%s"', $this->expectedRoleName));
    }

    public function hasPolicies(): bool
    {
        $this->tableNavigationTab->goToTab('Policies');

        return count($this->policies->getColumnValues(['Module'])) > 0;
    }

    public function hasAssignments(): bool
    {
        $this->tableNavigationTab->goToTab('Assignments');

        return count($this->assignments->getColumnValues(['User/Group'])) > 0;
    }

    public function verifyAssignments(array $expectedAssignments)
    {
        $this->goToTab('Assignment');

        $actualAssignments = $this->assignments->getColumnValues(['User/Group', 'Limitation']);

        foreach ($expectedAssignments as $expectedAssignment) {
            Assert::assertContains($expectedAssignment, $actualAssignments);
        }

        Assert::assertCount(count($expectedAssignments), $actualAssignments);
    }

    public function startAssigningUsers()
    {
        $this->goToTab('Assignments');
        $this->getHTMLPage()->find($this->getLocator('assignUsersButton'))->click();
    }

    public function deleteAssignments(array $itemNames)
    {
        $this->goToTab('Assignments');

        foreach ($itemNames as $item) {
            $this->assignments->getTableRow(['User/Group' => $item])->select();
        }

        $this->getHTMLPage()
            ->find($this->getLocator('deleteAssignmentButton'))
            ->click();

        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function deletePolicies(array $itemNames)
    {
        $this->goToTab('Policies');

        foreach ($itemNames as $item) {
            $this->policies->getTableRow(['Module' => $item])->select();
        }

        $this->getHTMLPage()
            ->find($this->getLocator('deletePoliciesButton'))
            ->click();

        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function createPolicy(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createPolicyButton'))->click();
    }

    public function editPolicy(string $moduleName, string $functionName): void
    {
        $this->policies->getTableRow(['Module' => $moduleName, 'Function' => $functionName])->edit();
    }
}
