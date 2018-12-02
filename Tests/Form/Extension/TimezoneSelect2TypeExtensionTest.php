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

use Fxp\Component\FormExtensions\Form\Extension\TimezoneSelect2TypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;

/**
 * Tests case for locale of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class TimezoneSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function getExtensionTypeName()
    {
        return TimezoneSelect2TypeExtension::class;
    }

    protected function getTypeName()
    {
        return TimezoneType::class;
    }

    protected function getSingleData()
    {
        return 'Europe/Paris';
    }

    protected function getValidSingleValue()
    {
        return 'Europe/Paris';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'Europe/Paris';
    }

    protected function getMultipleData()
    {
        return ['Europe/Paris', 'Europe/Rome'];
    }

    protected function getValidMultipleValue()
    {
        return ['Europe/Paris', 'Europe/Rome'];
    }

    protected function getValidAjaxMultipleValue()
    {
        return $this->getValidMultipleValue();
    }
}
