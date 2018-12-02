<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Tests\Doctrine\Form\Extension;

use Fxp\Component\FormExtensions\Doctrine\Form\Extension\FxpEntitySelect2TypeExtension;
use Fxp\Component\FormExtensions\Doctrine\Form\Type\EntityType;

/**
 * Tests case for entity of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpEntitySelect2TypeExtensionTest extends AbstractEntitySelect2TypeExtensionTest
{
    protected function getExtensionTypeName()
    {
        return FxpEntitySelect2TypeExtension::class;
    }

    protected function getTypeName()
    {
        return EntityType::class;
    }
}
