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

use Fxp\Component\FormExtensions\Form\Extension\LanguageSelect2TypeExtension;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;

/**
 * Tests case for language of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class LanguageSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function getExtensionTypeName()
    {
        return LanguageSelect2TypeExtension::class;
    }

    protected function getTypeName()
    {
        return LanguageType::class;
    }

    protected function getSingleData()
    {
        return 'fr';
    }

    protected function getValidSingleValue()
    {
        return 'fr';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'fr';
    }

    protected function getMultipleData()
    {
        return ['fr', 'en'];
    }

    protected function getValidMultipleValue()
    {
        return ['fr', 'en'];
    }

    protected function getValidAjaxMultipleValue()
    {
        return $this->getValidMultipleValue();
    }
}
