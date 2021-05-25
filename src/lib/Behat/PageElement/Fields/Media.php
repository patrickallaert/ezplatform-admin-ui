<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformAdminUi\Behat\PageElement\Fields;

use Behat\Mink\Session;
use EzSystems\Behat\Browser\FileUpload\FileUploadHelper;
use EzSystems\Behat\Browser\Locator\CSSLocator;
use EzSystems\Behat\Browser\Locator\CssLocatorBuilder;
use EzSystems\Behat\Browser\Locator\VisibleCSSLocator;
use FriendsOfBehat\SymfonyExtension\Mink\MinkParameters;
use PHPUnit\Framework\Assert;

class Media extends FieldTypeComponent
{
    /** @var \EzSystems\Behat\Browser\FileUpload\FileUploadHelper */
    private $fileUploadHelper;

    public function __construct(Session $session, MinkParameters $minkParameters, FileUploadHelper $fileUploadHelper)
    {
        parent::__construct($session, $minkParameters);
        $this->fileUploadHelper = $fileUploadHelper;
    }

    public function setValue(array $parameters): void
    {
        $fieldSelector = CSSLocatorBuilder::base($this->getLocator('fieldInput'))
            ->withParent($this->parentLocator)
            ->build();
        $this->getHTMLPage()->find($fieldSelector)->attachFile(
            $this->fileUploadHelper->getRemoteFileUploadPath($parameters['value'])
        );
    }

    public function verifyValueInItemView(array $values): void
    {
        $filename = str_replace('.zip', '', $values['value']);

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Media has wrong file name'
        );

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find(
                CSSLocatorBuilder::base($this->parentLocator)
                    ->withDescendant($this->getLocator('video'))
                    ->build()
            )->getAttribute('src'),
            'Media has wrong source'
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input[type=file]'),
            new VisibleCSSLocator('video', 'video'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezmedia';
    }
}
