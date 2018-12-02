<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Component\FormExtensions\Doctrine\Form\Extension;

use Fxp\Component\FormExtensions\Doctrine\Form\Type\EntityType;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpEntitySelect2TypeExtension extends AbstractEntitySelect2TypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [EntityType::class];
    }
}
