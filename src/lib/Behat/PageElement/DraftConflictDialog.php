<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement;

use EzSystems\Behat\Browser\Component\Component;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use Behat\Mink\Session;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table;

class DraftConflictDialog extends Component
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageElement\Table\Table */
    private $table;

    public function __construct(Session $session, MinkParameters $minkParameters, Table $table)
    {
        parent::__construct($session, $minkParameters);
        $this->table = $table->withParentLocator($this->getLocator('table'))->endConfiguration();
    }

    public function createNewDraft(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addDraft'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('dialog'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('dialog', '#version-draft-conflict-modal.ez-modal--version-draft-conflict.show .modal-content'),
            new VisibleCSSLocator('addDraft', '.ez-btn--add-draft'),
            new VisibleCSSLocator('table', '#version-draft-conflict-modal .modal-content'),
        ];
    }

    public function edit(string $versionNumber): void
    {
        $this->table->getTableRow(['Version' => $versionNumber])->edit();
    }
}
