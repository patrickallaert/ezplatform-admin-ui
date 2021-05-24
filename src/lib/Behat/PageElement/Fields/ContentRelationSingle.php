<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use eZ\Publish\Core\MVC\Symfony\SiteAccess\Router;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget;
use PHPUnit\Framework\Assert;

class ContentRelationSingle extends FieldTypeComponent
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\UniversalDiscoveryWidget */
    private $universalDiscoveryWidget;

    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    public function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('selectContent', '.ez-relations__cta-btn-label'),
            new VisibleCSSLocator('buttonRemove', '.ez-relations__table-action--remove'),
            new VisibleCSSLocator('relationRow', '.ez-relations__list tr'),
            new VisibleCSSLocator('columnHeader', 'tr:not(.ez-relations__table-header) th'),
        ];
    }

    public function __construct(Session $session, MinkParameters $minkParameters, UniversalDiscoveryWidget $universalDiscoveryWidget, Table $table)
    {
        parent::__construct($session, $minkParameters);
        $this->universalDiscoveryWidget = $universalDiscoveryWidget;
        $this->table = $table;
    }

    public function setValue(array $parameters): void
    {
        if (!$this->isRelationEmpty()) {
            $itemName = explode('/', $parameters['value'])[substr_count($parameters['value'], '/')];
            if (!$this->table->hasElement(['Name' => $itemName])) {
                $this->table->getTableRowByIndex(0)->select();
                $this->getHTMLPage()
                    ->find($this->parentLocator->withDescendant($this->getLocator('buttonRemove')))
                    ->click();
            } else {
                return;
            }
        }

        $this->getHTMLPage()
            ->find($this->parentLocator->withDescendant($this->getLocator('selectContent')))
            ->click();

        $this->universalDiscoveryWidget->selectContent($parameters['value']);
        $this->universalDiscoveryWidget->confirm();
    }

    public function getValue(): array
    {
        $names = $this->table->getColumnValues(['Name']);

        return [$names[0]['Name']];
    }

    public function setParentLocator(VisibleCSSLocator $locator): void
    {
        parent::setParentLocator($locator);
        $this->table = $this->table
            ->withParentLocator($this->parentLocator)
            ->withRowLocator($this->getLocator('relationRow'))
            ->withColumnLocator($this->getLocator('columnHeader'));
    }

    public function verifyValueInItemView(array $values): void
    {
        $explodedValue = explode('/', $values['value']);
        $value = $explodedValue[count($explodedValue) - 1];

        $viewPatternRegex = '/Single relation:[\w\/,: ]* %s [\w \/,:]*/';

        Assert::assertRegExp(
            sprintf($viewPatternRegex, $value),
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Field has wrong value'
        );
    }

    public function isRelationEmpty(): bool
    {
        $selectSelector = $this->parentLocator->withDescendant($this->getLocator('selectContent'));

        return $this->getHTMLPage()->findAll($selectSelector)->any();
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezobjectrelation';
    }
}
