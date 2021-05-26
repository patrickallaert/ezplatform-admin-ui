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
use PHPUnit\Framework\Assert;

class Image extends FieldTypeComponent
{
    /** @var \EzSystems\Behat\Browser\FileUpload\FileUploadHelper */
    protected $fileUploadHelper;

    public function __construct(Session $session, FileUploadHelper $fileUploadHelper)
    {
        parent::__construct($session);
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

        $alternativeText = str_replace('.zip', '', $parameters['value']);

        $alternaticveTextFieldLocator = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('alternativeText'))
            ->build();

        $this->getHTMLPage()
            ->find($alternaticveTextFieldLocator)
            ->setValue($alternativeText);
    }

    public function verifyValueInItemView(array $values): void
    {
        $filename = str_replace('.zip', '', $values['value']);

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->find($this->parentLocator)->getText(),
            'Image has wrong file name'
        );

        $fileFieldSelector = CSSLocatorBuilder::base($this->parentLocator)
            ->withDescendant($this->getLocator('image'))
            ->build();

        Assert::assertContains(
            $filename,
            $this->getHTMLPage()->setTimeout(5)->find($fileFieldSelector)->getAttribute('src'),
            'Image has wrong source'
        );
    }

    public function specifyLocators(): array
    {
        return [
            new CSSLocator('fieldInput', 'input[type=file]'),
            new VisibleCSSLocator('image', '.ez-field-preview__image-wrapper .ez-field-preview__image img'),
            new VisibleCSSLocator('alternativeText', 'input'),
        ];
    }

    public function getFieldTypeIdentifier(): string
    {
        return 'ezimage';
    }
}
