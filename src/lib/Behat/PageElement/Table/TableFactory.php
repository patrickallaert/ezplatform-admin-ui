<?php


namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Table;

use EzSystems\Behat\Browser\Element\ElementInterface;
use EzSystems\Behat\Browser\Locator\LocatorCollection;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use Behat\Mink\Session;

class TableFactory
{
    /** @var Session */
    private $session;

    /** @var MinkParameters */
    private $minkParameters;

    public function __construct(Session $session, MinkParameters $minkParameters)
    {
        $this->session = $session;
        $this->minkParameters = $minkParameters;
    }

    public function createRow(ElementInterface $element, LocatorCollection $locatorCollection): TableRow
    {
        return new TableRow(
            $this->session,
            $this->minkParameters,
            $element,
            $locatorCollection
        );
    }
}