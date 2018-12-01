<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Form\Extension;

use Fxp\Component\FormExtensions\Form\Extension\LocaleSelect2TypeExtension;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;

/**
 * Tests case for locale of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LocaleSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function getExtensionTypeName()
    {
        return LocaleSelect2TypeExtension::class;
    }

    protected function getTypeName()
    {
        return LocaleType::class;
    }

    protected function getSingleData()
    {
        return 'fr_FR';
    }

    protected function getValidSingleValue()
    {
        return 'fr_FR';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'fr_FR';
    }

    protected function getMultipleData()
    {
        return ['fr_FR', 'en_US'];
    }

    protected function getValidMultipleValue()
    {
        return ['fr_FR', 'en_US'];
    }

    protected function getValidAjaxMultipleValue()
    {
        return $this->getValidMultipleValue();
    }
}
