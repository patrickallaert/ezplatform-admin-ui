<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use EzSystems\EzPlatformAdminUi\Behat\PageObject\AdminUpdateItemPage;

class AdminUpdateContext implements Context
{
    /** @var \EzSystems\EzPlatformAdminUi\Behat\PageObject\AdminUpdateItemPage */
    private $adminUpdateItemPage;

    public function __construct(AdminUpdateItemPage $adminUpdateItemPage)
    {
        $this->adminUpdateItemPage = $adminUpdateItemPage;
    }

    /**
     * @When I set fields
     */
    public function iSetFields(TableNode $table): void
    {
        $this->adminUpdateItemPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->adminUpdateItemPage->fillFieldWithValue($row['label'], $row['value']);
        }
    }
}
