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

use Symfony\Component\Form\Extension\Core\Type\CountryType;

/**
 * Tests case for country of base choice select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class CountryBaseChoiceSelect2TypeExtensionTest extends AbstractBaseChoiceSelect2TypeExtensionTest
{
    protected function getExtensionTypeName()
    {
        return CountryType::class;
    }
}
